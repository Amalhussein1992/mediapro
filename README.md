# 📱 Media Pro - Social Media Management Platform

[![Laravel](https://img.shields.io/badge/Laravel-10-red.svg)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.1%2B-blue.svg)](https://php.net)
[![License](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)

## 🎯 Overview

Media Pro is a comprehensive social media management platform built with Laravel, providing a powerful API for mobile applications and an advanced web dashboard for administration.

**[📖 Arabic Documentation](README_AR.md)** | **[⚡ Quick Start](QUICK_START.md)** | **[🚀 Deployment Guide](DEPLOYMENT_GUIDE.md)** | **[📡 API Documentation](API_DOCUMENTATION.md)**

---

## ✨ Key Features

### 🔐 Authentication & Security
- Secure registration and login
- Laravel Sanctum API authentication
- Session and token management
- Advanced data protection

### 📝 Post Management
- Create and edit posts
- Automatic post scheduling
- Multi-platform support (Instagram, Facebook, Twitter, LinkedIn, TikTok)
- Image and video uploads
- Draft saving
- Performance analytics for each post

### 📊 Analytics & Insights
- Comprehensive dashboard with charts
- Real-time performance analytics
- Engagement metrics (Likes, Comments, Shares, Views)
- Best posting times
- Platform distribution
- Detailed exportable reports

### 🤖 AI Content Generation
- Professional caption generation
- Smart hashtag suggestions
- Content improvement
- New content idea generation
- Style variation (Professional, Casual, Enthusiastic)
- Platform-specific customization

### 🔗 Social Media Account Management
- Connect multiple accounts
- Permission management
- Status monitoring (Active/Inactive)
- Detailed account information
- Follower statistics

### 🎨 Brand Kit Management
- Create brand identity kits
- Store logos and colors
- Preferred fonts
- Ready-to-use templates
- Sync across all posts

### 💳 Subscription & Payment System
- Multiple subscription plans (Free, Basic, Pro, Enterprise)
- Secure payment
- Subscription management
- Payment history
- Auto-renewal

### 👥 User Management
- Comprehensive role system
- Custom permissions
- Team collaboration
- Activity tracking

---

## 🏗️ Technical Architecture

### Backend (Laravel)
```
app/
├── Http/
│   └── Controllers/
│       ├── API/
│       │   ├── AuthController.php         # Authentication
│       │   ├── PostController.php         # Posts
│       │   ├── AnalyticsController.php    # Analytics
│       │   ├── AIContentController.php    # AI Content
│       │   ├── BrandKitController.php     # Brand Kits
│       │   └── SocialAccountController.php # Social Accounts
│       └── Web/
│           ├── DashboardController.php
│           ├── AdminPostController.php
│           └── ...
└── Models/
    ├── User.php
    ├── Post.php
    ├── SocialAccount.php
    ├── Analytics.php
    ├── BrandKit.php
    ├── Subscription.php
    └── Payment.php
```

### Database Schema
```sql
users
├── id
├── name
├── email
├── password
├── current_subscription_plan_id
└── subscription_status

posts
├── id
├── user_id
├── content
├── media (JSON)
├── platforms (JSON)
├── status (draft/scheduled/published)
├── scheduled_at
├── published_at
└── analytics (JSON)

social_accounts
├── id
├── user_id
├── platform
├── account_name
├── access_token
├── is_active
└── followers_count

brand_kits
├── id
├── user_id
├── name
├── logo_url
├── colors (JSON)
├── fonts (JSON)
└── templates (JSON)

subscriptions
├── id
├── user_id
├── plan_id
├── status
├── start_date
└── end_date
```

---

## 🚀 API Endpoints

### Authentication
```
POST   /api/auth/register          # Register
POST   /api/auth/login            # Login
POST   /api/auth/logout           # Logout
GET    /api/auth/user             # Get current user
```

### Posts
```
GET    /api/posts                 # List posts
POST   /api/posts                 # Create post
GET    /api/posts/{id}            # Get post
PUT    /api/posts/{id}            # Update post
DELETE /api/posts/{id}            # Delete post
```

### Analytics
```
GET    /api/analytics/dashboard              # Dashboard
GET    /api/analytics/posts/{id}             # Post analytics
GET    /api/analytics/accounts/{id}          # Account analytics
GET    /api/analytics/trends                 # Trends
GET    /api/analytics/best-times             # Best posting times
```

### AI Content
```
POST   /api/ai/generate-caption              # Generate caption
POST   /api/ai/generate-hashtags             # Generate hashtags
POST   /api/ai/improve-content               # Improve content
POST   /api/ai/generate-ideas                # Generate ideas
```

### Brand Kits
```
GET    /api/brand-kits                       # List brand kits
POST   /api/brand-kits                       # Create brand kit
GET    /api/brand-kits/{id}                  # Get brand kit
PUT    /api/brand-kits/{id}                  # Update brand kit
DELETE /api/brand-kits/{id}                  # Delete brand kit
```

### Subscriptions
```
GET    /api/subscription-plans               # Subscription plans
POST   /api/subscriptions/subscribe          # Subscribe
GET    /api/subscriptions/current            # Current subscription
POST   /api/subscriptions/cancel             # Cancel subscription
```

**For complete documentation, see:** [API_DOCUMENTATION.md](API_DOCUMENTATION.md)

---

## 📱 Mobile App Integration Guide

### 1. Authentication Setup

```dart
// Flutter Example
class AuthService {
  final String baseUrl = 'http://your-domain.com/api';

  Future<User> login(String email, String password) async {
    final response = await http.post(
      Uri.parse('$baseUrl/auth/login'),
      body: {
        'email': email,
        'password': password,
      },
    );

    if (response.statusCode == 200) {
      final data = json.decode(response.body);
      // Save token
      await storage.write(key: 'token', value: data['data']['token']);
      return User.fromJson(data['data']['user']);
    }
    throw Exception('Login failed');
  }
}
```

### 2. Create Post

```dart
class PostService {
  Future<Post> createPost({
    required String content,
    required List<String> platforms,
    DateTime? scheduledAt,
  }) async {
    final token = await storage.read(key: 'token');

    final response = await http.post(
      Uri.parse('$baseUrl/posts'),
      headers: {
        'Authorization': 'Bearer $token',
        'Content-Type': 'application/json',
      },
      body: json.encode({
        'content': content,
        'platforms': platforms,
        'status': scheduledAt != null ? 'scheduled' : 'draft',
        'scheduled_at': scheduledAt?.toIso8601String(),
      }),
    );

    return Post.fromJson(json.decode(response.body)['data']);
  }
}
```

### 3. Get Analytics

```dart
class AnalyticsService {
  Future<DashboardData> getDashboard() async {
    final token = await storage.read(key: 'token');

    final response = await http.get(
      Uri.parse('$baseUrl/analytics/dashboard'),
      headers: {
        'Authorization': 'Bearer $token',
      },
    );

    return DashboardData.fromJson(json.decode(response.body)['data']);
  }
}
```

### 4. Use AI Features

```dart
class AIService {
  Future<String> generateCaption({
    required String topic,
    String tone = 'professional',
    String platform = 'instagram',
  }) async {
    final token = await storage.read(key: 'token');

    final response = await http.post(
      Uri.parse('$baseUrl/ai/generate-caption'),
      headers: {
        'Authorization': 'Bearer $token',
        'Content-Type': 'application/json',
      },
      body: json.encode({
        'topic': topic,
        'tone': tone,
        'platform': platform,
      }),
    );

    return json.decode(response.body)['data']['caption'];
  }
}
```

---

## 🎨 Admin Dashboard

### Features
- Real-time comprehensive statistics
- Interactive charts (Chart.js)
- User and post management
- Performance monitoring
- Modern and responsive design

### Access
```
http://your-domain.com/dashboard
```

**Default Credentials:**
- Email: `admin@admin.com`
- Password: `admin123`

⚠️ **Important**: Change immediately after first login!

### Interface
- **Home**: General statistics
- **Posts**: Post management
- **Users**: Account management
- **Analytics**: Detailed reports
- **Settings**: System settings

---

## 🔧 Installation & Setup

### Requirements
- PHP 8.1+
- Composer
- MySQL/PostgreSQL/SQLite
- Node.js & NPM

### Quick Installation

```bash
# 1. Clone project
git clone https://github.com/yourrepo/media-pro.git
cd media-pro/backend-laravel

# 2. Install dependencies
composer install
npm install

# 3. Setup environment
cp .env.example .env
php artisan key:generate

# 4. Configure database in .env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=media_pro
DB_USERNAME=root
DB_PASSWORD=

# 5. Run migrations
php artisan migrate
php artisan db:seed

# 6. Start server
php artisan serve
npm run dev
```

**🚀 For detailed installation guide:** See [QUICK_START.md](QUICK_START.md)

**📦 For production deployment:** See [DEPLOYMENT_GUIDE.md](DEPLOYMENT_GUIDE.md)

---

## 📦 Dependencies

### Backend
- **Laravel 10**: Main framework
- **Laravel Sanctum**: API authentication
- **Intervention Image**: Image processing
- **Laravel Excel**: Report export

### Frontend (Dashboard)
- **Tailwind CSS**: Styling
- **Chart.js**: Charts
- **Alpine.js**: Interactivity

---

## 🔒 Security

- ✅ Password encryption
- ✅ CSRF protection
- ✅ XSS protection
- ✅ API authentication with Tokens
- ✅ Rate limiting
- ✅ Comprehensive validation
- ✅ SQL injection protection

---

## 📈 Performance

- **Caching**: Redis/Memcached
- **Queues**: Laravel Queues
- **Database Indexing**: Optimized
- **CDN**: For media files
- **API Response Time**: < 200ms

---

## 🧪 Testing

```bash
# Run all tests
php artisan test

# Run specific test
php artisan test --filter=PostTest

# With coverage
php artisan test --coverage
```

---

## 📝 Future Roadmap

- [ ] WebSockets for real-time updates
- [ ] Payment gateway integration (Stripe, PayPal)
- [ ] Enhanced AI with OpenAI
- [ ] Support for longer videos
- [ ] Advanced image editor
- [ ] PDF reports
- [ ] Push notifications for mobile
- [ ] Additional language support

---

## 🧰 Available Tools

### Postman Collection
Import `Media_Pro_API.postman_collection.json` for easy API testing.

### Environment Variables
Configure in `.env` file - never commit to git!

### Artisan Commands
```bash
php artisan cache:clear        # Clear cache
php artisan route:list         # List all routes
php artisan migrate:fresh      # Reset database
php artisan queue:work         # Start queue worker
php artisan schedule:run       # Run scheduled tasks
```

---

## 📞 Support & Contact

- **Email**: support@mediapro.com
- **Documentation**: [docs.mediapro.com](docs.mediapro.com)
- **GitHub**: [github.com/mediapro](github.com/mediapro)
- **Issues**: Create an issue on GitHub

---

## 📜 License

This project is licensed under the MIT License.

---

## 👥 Contributors

- Lead Developer: Your Name
- Development Team: Media Pro Team

---

## 🌟 Acknowledgments

Thank you for using Media Pro! We're committed to providing the best social media management experience.

---

## 📚 Documentation

- **[⚡ Quick Start Guide](QUICK_START.md)** - Get started in 5 minutes
- **[🚀 Deployment Guide](DEPLOYMENT_GUIDE.md)** - Production deployment
- **[📡 API Documentation](API_DOCUMENTATION.md)** - Complete API reference
- **[🌐 Arabic Documentation](README_AR.md)** - التوثيق بالعربية

---

## 🔗 Quick Links

- [Features Overview](#-key-features)
- [API Endpoints](#-api-endpoints)
- [Installation Guide](#-installation--setup)
- [Mobile Integration](#-mobile-app-integration-guide)
- [Admin Dashboard](#-admin-dashboard)
- [Security](#-security)
- [Testing](#-testing)

---

**Built with ❤️ by Media Pro Team**

---

## 🤝 Contributing

We welcome contributions! Please:

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

---

## 🐛 Bug Reports

If you discover a bug, please create an issue on GitHub with:
- Clear description
- Steps to reproduce
- Expected vs actual behavior
- Screenshots if applicable

---

## 💡 Feature Requests

Have an idea? We'd love to hear it! Open an issue with the `enhancement` label.

---

## 📊 Project Stats

- **API Endpoints**: 50+
- **Database Tables**: 12
- **Supported Platforms**: 5 (Instagram, Facebook, Twitter, LinkedIn, TikTok)
- **Subscription Plans**: 4 (Free, Basic, Pro, Enterprise)
- **Languages**: English, Arabic
- **Laravel Version**: 10
- **PHP Version**: 8.1+

---

**Happy Coding! 🎉**
