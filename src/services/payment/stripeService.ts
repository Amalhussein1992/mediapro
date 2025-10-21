/**
 * Stripe Payment Service
 * Secure payment processing with Stripe
 */

import { API_CONFIG } from '../../config/api.config';
import AsyncStorage from '@react-native-async-storage/async-storage';

export interface StripeCardElement {
  complete: boolean;
  error?: { message: string };
}

export interface StripePaymentMethod {
  id: string;
  type: string;
  card?: {
    brand: string;
    last4: string;
    exp_month: number;
    exp_year: number;
  };
}

class StripeService {
  private publishableKey: string = '';
  private initialized: boolean = false;

  /**
   * Initialize Stripe
   */
  async initialize(): Promise<void> {
    if (this.initialized) return;

    try {
      // Get publishable key from backend (more secure)
      const response = await fetch(`${API_CONFIG.BASE_URL}/stripe/config`);
      const data = await response.json();

      this.publishableKey = data.publishable_key;
      this.initialized = true;

      console.log('✅ Stripe initialized with publishable key');
    } catch (error) {
      console.error('❌ Failed to initialize Stripe:', error);
      throw error;
    }
  }

  /**
   * Create Payment Intent for one-time payment
   */
  async createPaymentIntent(
    amount: number,
    currency: string = 'USD',
    metadata?: Record<string, string>
  ): Promise<string> {
    try {
      const token = await AsyncStorage.getItem('@auth_token');

      const response = await fetch(`${API_CONFIG.BASE_URL}/stripe/create-payment-intent`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'Authorization': `Bearer ${token}`
        },
        body: JSON.stringify({
          amount,
          currency,
          metadata
        })
      });

      if (!response.ok) {
        const error = await response.json();
        throw new Error(error.message || 'Failed to create payment intent');
      }

      const data = await response.json();
      return data.client_secret;
    } catch (error: any) {
      console.error('Create payment intent error:', error);
      throw error;
    }
  }

  /**
   * Create Subscription
   */
  async createSubscription(
    planId: number,
    stripePriceId: string
  ): Promise<{
    subscription_id: string;
    client_secret?: string;
    payment_intent_id?: string;
  }> {
    try {
      const token = await AsyncStorage.getItem('@auth_token');

      const response = await fetch(`${API_CONFIG.BASE_URL}/stripe/create-subscription`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'Authorization': `Bearer ${token}`
        },
        body: JSON.stringify({
          plan_id: planId,
          price_id: stripePriceId
        })
      });

      if (!response.ok) {
        const error = await response.json();
        throw new Error(error.message || 'Failed to create subscription');
      }

      const data = await response.json();
      return {
        subscription_id: data.subscription.id,
        client_secret: data.client_secret,
        payment_intent_id: data.payment_intent_id
      };
    } catch (error: any) {
      console.error('Create subscription error:', error);
      throw error;
    }
  }

  /**
   * Add Payment Method
   */
  async addPaymentMethod(
    paymentMethodId: string
  ): Promise<StripePaymentMethod> {
    try {
      const token = await AsyncStorage.getItem('@auth_token');

      const response = await fetch(`${API_CONFIG.BASE_URL}/stripe/add-payment-method`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'Authorization': `Bearer ${token}`
        },
        body: JSON.stringify({
          payment_method_id: paymentMethodId
        })
      });

      if (!response.ok) {
        const error = await response.json();
        throw new Error(error.message || 'Failed to add payment method');
      }

      const data = await response.json();
      return data.payment_method;
    } catch (error: any) {
      console.error('Add payment method error:', error);
      throw error;
    }
  }

  /**
   * Get Payment Methods
   */
  async getPaymentMethods(): Promise<StripePaymentMethod[]> {
    try {
      const token = await AsyncStorage.getItem('@auth_token');

      const response = await fetch(`${API_CONFIG.BASE_URL}/stripe/payment-methods`, {
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'Authorization': `Bearer ${token}`
        }
      });

      if (!response.ok) {
        const error = await response.json();
        throw new Error(error.message || 'Failed to get payment methods');
      }

      const data = await response.json();
      return data.payment_methods || [];
    } catch (error: any) {
      console.error('Get payment methods error:', error);
      return [];
    }
  }

  /**
   * Set Default Payment Method
   */
  async setDefaultPaymentMethod(paymentMethodId: string): Promise<boolean> {
    try {
      const token = await AsyncStorage.getItem('@auth_token');

      const response = await fetch(`${API_CONFIG.BASE_URL}/stripe/set-default-payment-method`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'Authorization': `Bearer ${token}`
        },
        body: JSON.stringify({
          payment_method_id: paymentMethodId
        })
      });

      const data = await response.json();
      return data.success;
    } catch (error: any) {
      console.error('Set default payment method error:', error);
      return false;
    }
  }

  /**
   * Delete Payment Method
   */
  async deletePaymentMethod(paymentMethodId: string): Promise<boolean> {
    try {
      const token = await AsyncStorage.getItem('@auth_token');

      const response = await fetch(`${API_CONFIG.BASE_URL}/stripe/delete-payment-method`, {
        method: 'DELETE',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'Authorization': `Bearer ${token}`
        },
        body: JSON.stringify({
          payment_method_id: paymentMethodId
        })
      });

      const data = await response.json();
      return data.success;
    } catch (error: any) {
      console.error('Delete payment method error:', error);
      return false;
    }
  }

  /**
   * Update Subscription
   */
  async updateSubscription(
    subscriptionId: string,
    newPriceId: string
  ): Promise<boolean> {
    try {
      const token = await AsyncStorage.getItem('@auth_token');

      const response = await fetch(`${API_CONFIG.BASE_URL}/stripe/update-subscription`, {
        method: 'PUT',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'Authorization': `Bearer ${token}`
        },
        body: JSON.stringify({
          subscription_id: subscriptionId,
          new_price_id: newPriceId
        })
      });

      const data = await response.json();
      return data.success;
    } catch (error: any) {
      console.error('Update subscription error:', error);
      return false;
    }
  }

  /**
   * Cancel Subscription
   */
  async cancelSubscription(subscriptionId: string): Promise<boolean> {
    try {
      const token = await AsyncStorage.getItem('@auth_token');

      const response = await fetch(`${API_CONFIG.BASE_URL}/stripe/cancel-subscription`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'Authorization': `Bearer ${token}`
        },
        body: JSON.stringify({
          subscription_id: subscriptionId
        })
      });

      const data = await response.json();
      return data.success;
    } catch (error: any) {
      console.error('Cancel subscription error:', error);
      return false;
    }
  }

  /**
   * Get Subscription Details
   */
  async getSubscription(subscriptionId: string): Promise<any> {
    try {
      const token = await AsyncStorage.getItem('@auth_token');

      const response = await fetch(`${API_CONFIG.BASE_URL}/stripe/subscription/${subscriptionId}`, {
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'Authorization': `Bearer ${token}`
        }
      });

      if (!response.ok) {
        throw new Error('Failed to get subscription');
      }

      const data = await response.json();
      return data.subscription;
    } catch (error: any) {
      console.error('Get subscription error:', error);
      return null;
    }
  }

  /**
   * Get Invoices
   */
  async getInvoices(limit: number = 10): Promise<any[]> {
    try {
      const token = await AsyncStorage.getItem('@auth_token');

      const response = await fetch(`${API_CONFIG.BASE_URL}/stripe/invoices?limit=${limit}`, {
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'Authorization': `Bearer ${token}`
        }
      });

      if (!response.ok) {
        throw new Error('Failed to get invoices');
      }

      const data = await response.json();
      return data.invoices || [];
    } catch (error: any) {
      console.error('Get invoices error:', error);
      return [];
    }
  }
}

export const stripeService = new StripeService();
export default stripeService;
