/**
 * Unified Payment Service
 * Handles Stripe, Google Play, and Apple IAP payments
 */

import { Platform } from 'react-native';
import { stripeService } from './stripeService';
import { googlePlayService } from './googlePlayService';
import { appleIAPService } from './appleIAPService';

export type PaymentProvider = 'stripe' | 'google_play' | 'apple_iap';

export interface PaymentResult {
  success: boolean;
  provider: PaymentProvider;
  transactionId?: string;
  subscriptionId?: string;
  error?: string;
}

export interface SubscriptionPlan {
  id: number;
  name: string;
  price: number;
  currency: string;
  billing_cycle: 'monthly' | 'yearly';
  stripe_price_id?: string;
  google_play_sku?: string;
  apple_product_id?: string;
}

class UnifiedPaymentService {
  private initialized = false;

  /**
   * Initialize payment providers based on platform
   */
  async initialize(): Promise<void> {
    if (this.initialized) return;

    try {
      if (Platform.OS === 'ios') {
        await appleIAPService.initialize();
        console.log('✅ Apple IAP initialized');
      } else if (Platform.OS === 'android') {
        await googlePlayService.initialize();
        console.log('✅ Google Play Billing initialized');
      }

      // Stripe is available on all platforms
      await stripeService.initialize();
      console.log('✅ Stripe initialized');

      this.initialized = true;
    } catch (error) {
      console.error('❌ Failed to initialize payment service:', error);
      throw error;
    }
  }

  /**
   * Get recommended payment provider for current platform
   */
  getRecommendedProvider(): PaymentProvider {
    if (Platform.OS === 'ios') {
      return 'apple_iap';
    } else if (Platform.OS === 'android') {
      return 'google_play';
    } else {
      return 'stripe';
    }
  }

  /**
   * Get available payment providers for current platform
   */
  getAvailableProviders(): PaymentProvider[] {
    const providers: PaymentProvider[] = ['stripe'];

    if (Platform.OS === 'ios') {
      providers.push('apple_iap');
    } else if (Platform.OS === 'android') {
      providers.push('google_play');
    }

    return providers;
  }

  /**
   * Get subscription products from all providers
   */
  async getSubscriptionPlans(): Promise<SubscriptionPlan[]> {
    try {
      // Get plans from backend
      const response = await fetch('/api/subscription-plans', {
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json'
        }
      });

      const data = await response.json();
      return data.data || [];
    } catch (error) {
      console.error('Failed to get subscription plans:', error);
      return [];
    }
  }

  /**
   * Purchase subscription using specified provider
   */
  async purchaseSubscription(
    plan: SubscriptionPlan,
    provider?: PaymentProvider
  ): Promise<PaymentResult> {
    if (!this.initialized) {
      await this.initialize();
    }

    // Use recommended provider if not specified
    const selectedProvider = provider || this.getRecommendedProvider();

    try {
      switch (selectedProvider) {
        case 'stripe':
          return await this.purchaseWithStripe(plan);

        case 'google_play':
          return await this.purchaseWithGooglePlay(plan);

        case 'apple_iap':
          return await this.purchaseWithAppleIAP(plan);

        default:
          throw new Error(`Unsupported payment provider: ${selectedProvider}`);
      }
    } catch (error: any) {
      console.error(`Payment failed with ${selectedProvider}:`, error);

      return {
        success: false,
        provider: selectedProvider,
        error: error.message || 'Payment failed'
      };
    }
  }

  /**
   * Purchase with Stripe
   */
  private async purchaseWithStripe(plan: SubscriptionPlan): Promise<PaymentResult> {
    try {
      if (!plan.stripe_price_id) {
        throw new Error('Stripe price ID not configured for this plan');
      }

      const result = await stripeService.createSubscription(
        plan.id,
        plan.stripe_price_id
      );

      return {
        success: true,
        provider: 'stripe',
        subscriptionId: result.subscription_id,
        transactionId: result.payment_intent_id
      };
    } catch (error: any) {
      throw new Error(`Stripe payment failed: ${error.message}`);
    }
  }

  /**
   * Purchase with Google Play
   */
  private async purchaseWithGooglePlay(plan: SubscriptionPlan): Promise<PaymentResult> {
    if (Platform.OS !== 'android') {
      throw new Error('Google Play Billing only available on Android');
    }

    try {
      if (!plan.google_play_sku) {
        throw new Error('Google Play SKU not configured for this plan');
      }

      const result = await googlePlayService.purchaseSubscription(plan.google_play_sku);

      return {
        success: true,
        provider: 'google_play',
        transactionId: result.purchase.purchaseToken
      };
    } catch (error: any) {
      throw new Error(`Google Play payment failed: ${error.message}`);
    }
  }

  /**
   * Purchase with Apple IAP
   */
  private async purchaseWithAppleIAP(plan: SubscriptionPlan): Promise<PaymentResult> {
    if (Platform.OS !== 'ios') {
      throw new Error('Apple In-App Purchase only available on iOS');
    }

    try {
      if (!plan.apple_product_id) {
        throw new Error('Apple Product ID not configured for this plan');
      }

      await appleIAPService.purchaseSubscription(plan.apple_product_id);

      // Purchase will be handled by listener, return pending status
      return {
        success: true,
        provider: 'apple_iap',
        transactionId: 'pending' // Will be updated by listener
      };
    } catch (error: any) {
      throw new Error(`Apple IAP payment failed: ${error.message}`);
    }
  }

  /**
   * Restore previous purchases (for Apple and Google Play)
   */
  async restorePurchases(): Promise<PaymentResult> {
    try {
      if (Platform.OS === 'ios') {
        const purchases = await appleIAPService.restorePurchases();
        return {
          success: purchases.length > 0,
          provider: 'apple_iap',
          transactionId: purchases[0]?.transactionId
        };
      } else if (Platform.OS === 'android') {
        const purchases = await googlePlayService.restorePurchases();
        return {
          success: purchases.length > 0,
          provider: 'google_play',
          transactionId: purchases[0]?.purchaseToken
        };
      } else {
        throw new Error('Restore purchases not available on this platform');
      }
    } catch (error: any) {
      return {
        success: false,
        provider: this.getRecommendedProvider(),
        error: error.message
      };
    }
  }

  /**
   * Cancel subscription
   */
  async cancelSubscription(): Promise<boolean> {
    try {
      const response = await fetch('/api/subscriptions/cancel', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json'
        }
      });

      const data = await response.json();
      return data.success;
    } catch (error) {
      console.error('Failed to cancel subscription:', error);
      return false;
    }
  }

  /**
   * Get current subscription status
   */
  async getSubscriptionStatus(): Promise<any> {
    try {
      const response = await fetch('/api/subscriptions/status', {
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json'
        }
      });

      const data = await response.json();
      return data.data;
    } catch (error) {
      console.error('Failed to get subscription status:', error);
      return null;
    }
  }

  /**
   * Cleanup resources
   */
  cleanup(): void {
    if (Platform.OS === 'ios') {
      appleIAPService.cleanup();
    } else if (Platform.OS === 'android') {
      googlePlayService.cleanup();
    }

    this.initialized = false;
  }
}

export const unifiedPaymentService = new UnifiedPaymentService();
export default unifiedPaymentService;
