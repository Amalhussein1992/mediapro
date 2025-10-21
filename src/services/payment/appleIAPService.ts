/**
 * Apple In-App Purchase Service
 * Handles subscriptions through Apple App Store
 */

import { Platform } from 'react-native';
import * as RNIap from 'react-native-iap';
import { API_CONFIG } from '../../config/api.config';
import AsyncStorage from '@react-native-async-storage/async-storage';

export interface AppleProduct {
  productId: string;
  title: string;
  description: string;
  price: string;
  currency: string;
  subscriptionPeriodNumberIOS?: string;
  subscriptionPeriodUnitIOS?: string;
}

export interface ApplePurchase {
  productId: string;
  transactionId: string;
  transactionDate: number;
  transactionReceipt: string;
  originalTransactionId?: string;
}

class AppleIAPService {
  private initialized: boolean = false;
  private purchaseUpdateSubscription: any = null;
  private purchaseErrorSubscription: any = null;

  /**
   * Initialize Apple In-App Purchase
   */
  async initialize(): Promise<void> {
    if (Platform.OS !== 'ios') {
      console.log('⏭️ Apple IAP skipped (not iOS)');
      return;
    }

    if (this.initialized) return;

    try {
      await RNIap.initConnection();

      // Setup purchase listeners
      this.setupPurchaseListeners();

      this.initialized = true;
      console.log('✅ Apple IAP initialized');
    } catch (error) {
      console.error('❌ Failed to initialize Apple IAP:', error);
      throw error;
    }
  }

  /**
   * Setup purchase update listeners
   */
  private setupPurchaseListeners(): void {
    this.purchaseUpdateSubscription = RNIap.purchaseUpdatedListener(
      async (purchase: any) => {
        console.log('📦 Purchase updated:', purchase);

        const receipt = purchase.transactionReceipt;

        if (receipt) {
          try {
            // Verify receipt on backend
            const isValid = await this.verifyReceipt(receipt);

            if (isValid) {
              // Finish transaction
              await RNIap.finishTransaction(purchase, false);
              console.log('✅ Transaction finished');
            } else {
              console.error('❌ Receipt verification failed');
            }
          } catch (error) {
            console.error('❌ Error processing purchase:', error);
          }
        }
      }
    );

    this.purchaseErrorSubscription = RNIap.purchaseErrorListener(
      (error: any) => {
        console.error('❌ Purchase error:', error);

        if (error.code === 'E_USER_CANCELLED') {
          console.log('ℹ️ User cancelled the purchase');
        } else if (error.code === 'E_ALREADY_OWNED') {
          console.log('ℹ️ User already owns this item');
        } else if (error.code === 'E_ITEM_UNAVAILABLE') {
          console.error('❌ Item is unavailable');
        }
      }
    );
  }

  /**
   * Get available subscription products
   */
  async getProducts(skus: string[]): Promise<AppleProduct[]> {
    if (Platform.OS !== 'ios') {
      throw new Error('Apple IAP only available on iOS');
    }

    try {
      const products = await RNIap.getSubscriptions({ skus });
      return products.map(p => ({
        productId: p.productId,
        title: p.title || '',
        description: p.description || '',
        price: p.localizedPrice,
        currency: p.currency,
        subscriptionPeriodNumberIOS: p.subscriptionPeriodNumberIOS,
        subscriptionPeriodUnitIOS: p.subscriptionPeriodUnitIOS
      }));
    } catch (error: any) {
      console.error('Failed to get products:', error);
      throw error;
    }
  }

  /**
   * Purchase subscription
   */
  async purchaseSubscription(productId: string): Promise<{
    success: boolean;
    purchase?: ApplePurchase;
    error?: string;
  }> {
    if (Platform.OS !== 'ios') {
      throw new Error('Apple IAP only available on iOS');
    }

    try {
      // Check if already subscribed
      const availablePurchases = await RNIap.getAvailablePurchases();
      const existingPurchase = availablePurchases.find(
        p => p.productId === productId
      );

      if (existingPurchase) {
        console.log('ℹ️ User already owns this subscription');

        // Verify and activate on backend
        if (existingPurchase.transactionReceipt) {
          await this.verifyReceipt(existingPurchase.transactionReceipt);
        }

        return {
          success: true,
          purchase: {
            productId: existingPurchase.productId,
            transactionId: existingPurchase.transactionId || '',
            transactionDate: existingPurchase.transactionDate || Date.now(),
            transactionReceipt: existingPurchase.transactionReceipt || '',
            originalTransactionId: existingPurchase.originalTransactionIdentifierIOS
          }
        };
      }

      // Request new purchase
      await RNIap.requestSubscription({ sku: productId });

      console.log('✅ Purchase initiated for:', productId);

      // Purchase will be handled by listener
      return {
        success: true
      };
    } catch (error: any) {
      console.error('Purchase failed:', error);

      let errorMessage = 'Purchase failed';

      if (error.code === 'E_USER_CANCELLED') {
        errorMessage = 'Purchase cancelled by user';
      } else if (error.code === 'E_ALREADY_OWNED') {
        errorMessage = 'You already own this subscription';
      } else if (error.code === 'E_ITEM_UNAVAILABLE') {
        errorMessage = 'Subscription unavailable';
      } else if (error.code === 'E_NETWORK_ERROR') {
        errorMessage = 'Network error, please try again';
      }

      return {
        success: false,
        error: errorMessage
      };
    }
  }

  /**
   * Verify receipt on backend
   */
  private async verifyReceipt(receipt: string): Promise<boolean> {
    try {
      const token = await AsyncStorage.getItem('@auth_token');

      const response = await fetch(`${API_CONFIG.BASE_URL}/apple-iap/verify`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'Authorization': `Bearer ${token}`
        },
        body: JSON.stringify({
          receipt
        })
      });

      if (!response.ok) {
        const error = await response.json();
        console.error('Verification failed:', error);
        return false;
      }

      const data = await response.json();
      console.log('✅ Receipt verified:', data);

      return data.success === true;
    } catch (error) {
      console.error('Verification error:', error);
      return false;
    }
  }

  /**
   * Restore previous purchases
   */
  async restorePurchases(): Promise<ApplePurchase[]> {
    if (Platform.OS !== 'ios') {
      throw new Error('Apple IAP only available on iOS');
    }

    try {
      // Clear all transactions first
      await RNIap.clearTransactionIOS();

      // Get available purchases
      const purchases = await RNIap.getAvailablePurchases();

      console.log('📦 Found', purchases.length, 'previous purchases');

      // Verify each receipt on backend
      for (const purchase of purchases) {
        if (purchase.transactionReceipt) {
          await this.verifyReceipt(purchase.transactionReceipt);
        }
      }

      return purchases.map(p => ({
        productId: p.productId,
        transactionId: p.transactionId || '',
        transactionDate: p.transactionDate || Date.now(),
        transactionReceipt: p.transactionReceipt || '',
        originalTransactionId: p.originalTransactionIdentifierIOS
      }));
    } catch (error: any) {
      console.error('Restore purchases error:', error);
      throw error;
    }
  }

  /**
   * Check subscription status
   */
  async checkSubscriptionStatus(productId: string): Promise<boolean> {
    try {
      const purchases = await RNIap.getAvailablePurchases();
      const subscription = purchases.find(p => p.productId === productId);

      return !!subscription;
    } catch (error) {
      console.error('Check subscription error:', error);
      return false;
    }
  }

  /**
   * Get current receipt
   */
  async getCurrentReceipt(): Promise<string | null> {
    if (Platform.OS !== 'ios') {
      return null;
    }

    try {
      const receipt = await RNIap.requestReceipt();
      return receipt || null;
    } catch (error) {
      console.error('Get receipt error:', error);
      return null;
    }
  }

  /**
   * Clear finished transactions
   */
  async clearTransactions(): Promise<void> {
    if (Platform.OS !== 'ios') {
      return;
    }

    try {
      await RNIap.clearTransactionIOS();
      console.log('✅ Transactions cleared');
    } catch (error) {
      console.error('Clear transactions error:', error);
    }
  }

  /**
   * Cleanup resources
   */
  cleanup(): void {
    if (this.purchaseUpdateSubscription) {
      this.purchaseUpdateSubscription.remove();
      this.purchaseUpdateSubscription = null;
    }

    if (this.purchaseErrorSubscription) {
      this.purchaseErrorSubscription.remove();
      this.purchaseErrorSubscription = null;
    }

    RNIap.endConnection();
    this.initialized = false;

    console.log('🧹 Apple IAP cleanup complete');
  }
}

export const appleIAPService = new AppleIAPService();
export default appleIAPService;
