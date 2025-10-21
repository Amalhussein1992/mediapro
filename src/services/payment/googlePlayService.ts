/**
 * Google Play Billing Service
 * Handles subscriptions through Google Play Store
 */

import { Platform } from 'react-native';
import * as RNIap from 'react-native-iap';
import { API_CONFIG } from '../../config/api.config';
import AsyncStorage from '@react-native-async-storage/async-storage';

export interface GooglePlayProduct {
  productId: string;
  title: string;
  description: string;
  price: string;
  currency: string;
  subscriptionPeriod?: string;
}

export interface GooglePlayPurchase {
  productId: string;
  purchaseToken: string;
  transactionId: string;
  transactionDate: number;
  transactionReceipt: string;
}

class GooglePlayService {
  private initialized: boolean = false;
  private purchaseUpdateSubscription: any = null;
  private purchaseErrorSubscription: any = null;

  /**
   * Initialize Google Play Billing
   */
  async initialize(): Promise<void> {
    if (Platform.OS !== 'android') {
      console.log('⏭️ Google Play Billing skipped (not Android)');
      return;
    }

    if (this.initialized) return;

    try {
      await RNIap.initConnection();
      await RNIap.flushFailedPurchasesCachedAsPendingAndroid();

      // Setup purchase listeners
      this.setupPurchaseListeners();

      this.initialized = true;
      console.log('✅ Google Play Billing initialized');
    } catch (error) {
      console.error('❌ Failed to initialize Google Play Billing:', error);
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
            // Verify purchase on backend
            const isValid = await this.verifyPurchase(purchase);

            if (isValid) {
              // Acknowledge purchase (required for consumables and subscriptions)
              await RNIap.acknowledgePurchaseAndroid(purchase.purchaseToken);
              console.log('✅ Purchase acknowledged');

              // Finish transaction
              await RNIap.finishTransaction(purchase, false);
              console.log('✅ Transaction finished');
            } else {
              console.error('❌ Purchase verification failed');
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
  async getProducts(skus: string[]): Promise<GooglePlayProduct[]> {
    if (Platform.OS !== 'android') {
      throw new Error('Google Play Billing only available on Android');
    }

    try {
      const products = await RNIap.getSubscriptions({ skus });
      return products.map(p => ({
        productId: p.productId,
        title: p.title,
        description: p.description,
        price: p.localizedPrice,
        currency: p.currency,
        subscriptionPeriod: p.subscriptionPeriodAndroid
      }));
    } catch (error: any) {
      console.error('Failed to get products:', error);
      throw error;
    }
  }

  /**
   * Purchase subscription
   */
  async purchaseSubscription(sku: string): Promise<{
    success: boolean;
    purchase?: GooglePlayPurchase;
    error?: string;
  }> {
    if (Platform.OS !== 'android') {
      throw new Error('Google Play Billing only available on Android');
    }

    try {
      // Check if already subscribed
      const availablePurchases = await RNIap.getAvailablePurchases();
      const existingPurchase = availablePurchases.find(
        p => p.productId === sku
      );

      if (existingPurchase) {
        console.log('ℹ️ User already owns this subscription');

        // Verify and activate on backend
        await this.verifyPurchase(existingPurchase as any);

        return {
          success: true,
          purchase: {
            productId: existingPurchase.productId,
            purchaseToken: existingPurchase.purchaseToken || '',
            transactionId: existingPurchase.transactionId || '',
            transactionDate: existingPurchase.transactionDate || Date.now(),
            transactionReceipt: existingPurchase.transactionReceipt || ''
          }
        };
      }

      // Request new purchase
      const purchase = await RNIap.requestSubscription({ sku });

      console.log('✅ Purchase initiated:', purchase);

      return {
        success: true,
        purchase: {
          productId: purchase.productId,
          purchaseToken: purchase.purchaseToken,
          transactionId: purchase.transactionId || '',
          transactionDate: purchase.transactionDate || Date.now(),
          transactionReceipt: purchase.transactionReceipt || ''
        }
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
   * Verify purchase on backend
   */
  private async verifyPurchase(purchase: any): Promise<boolean> {
    try {
      const token = await AsyncStorage.getItem('@auth_token');

      const response = await fetch(`${API_CONFIG.BASE_URL}/google-play/verify`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'Authorization': `Bearer ${token}`
        },
        body: JSON.stringify({
          purchase_token: purchase.purchaseToken,
          product_id: purchase.productId,
          package_name: 'com.mediapro.app' // Your app's package name
        })
      });

      if (!response.ok) {
        const error = await response.json();
        console.error('Verification failed:', error);
        return false;
      }

      const data = await response.json();
      console.log('✅ Purchase verified:', data);

      return data.success === true;
    } catch (error) {
      console.error('Verification error:', error);
      return false;
    }
  }

  /**
   * Restore previous purchases
   */
  async restorePurchases(): Promise<GooglePlayPurchase[]> {
    if (Platform.OS !== 'android') {
      throw new Error('Google Play Billing only available on Android');
    }

    try {
      const purchases = await RNIap.getAvailablePurchases();

      console.log('📦 Found', purchases.length, 'previous purchases');

      // Verify each purchase on backend
      for (const purchase of purchases) {
        await this.verifyPurchase(purchase);
      }

      return purchases.map(p => ({
        productId: p.productId,
        purchaseToken: p.purchaseToken || '',
        transactionId: p.transactionId || '',
        transactionDate: p.transactionDate || Date.now(),
        transactionReceipt: p.transactionReceipt || ''
      }));
    } catch (error: any) {
      console.error('Restore purchases error:', error);
      throw error;
    }
  }

  /**
   * Check subscription status
   */
  async checkSubscriptionStatus(sku: string): Promise<boolean> {
    try {
      const purchases = await RNIap.getAvailablePurchases();
      const subscription = purchases.find(p => p.productId === sku);

      return !!subscription;
    } catch (error) {
      console.error('Check subscription error:', error);
      return false;
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

    console.log('🧹 Google Play Billing cleanup complete');
  }
}

export const googlePlayService = new GooglePlayService();
export default googlePlayService;
