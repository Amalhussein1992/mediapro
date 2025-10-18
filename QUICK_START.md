# ⚡ Quick Start Guide - Media Pro API

Get the Media Pro API up and running in 5 minutes!

## Prerequisites

- PHP 8.1+
- Composer
- Node.js & NPM

## Installation

```bash
# 1. Navigate to project
cd backend-laravel

# 2. Install dependencies
composer install
npm install

# 3. Setup environment
cp .env.example .env
php artisan key:generate

# 4. Setup database (SQLite - easiest)
# Already configured in .env.example
touch database/database.sqlite

# 5. Run migrations and seed data
php artisan migrate
php artisan db:seed

# 6. Create storage link
php artisan storage:link

# 7. Build assets
npm run dev

# 8. Start server
php artisan serve
```

## 🎉 Done!

Your API is now running at: **http://127.0.0.1:8000**

## Test It!

### Web Dashboard
Visit: http://127.0.0.1:8000/dashboard

**Login credentials:**
- Email: `admin@admin.com`
- Password: `admin123`

### API Testing

**Register a new user:**
```bash
curl -X POST http://127.0.0.1:8000/api/auth/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Test User",
    "email": "test@example.com",
    "password": "password123",
    "password_confirmation": "password123"
  }'
```

**Login:**
```bash
curl -X POST http://127.0.0.1:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "test@example.com",
    "password": "password123"
  }'
```

Copy the token from response and use it for authenticated requests:

**Get user info:**
```bash
curl -X GET http://127.0.0.1:8000/api/auth/user \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

**Create a post:**
```bash
curl -X POST http://127.0.0.1:8000/api/posts \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Content-Type: application/json" \
  -d '{
    "content": "My first post! 🚀",
    "platforms": ["instagram", "facebook"],
    "status": "draft"
  }'
```

## 📚 Next Steps

- **Full API Documentation**: See `API_DOCUMENTATION.md`
- **Postman Collection**: Import `Media_Pro_API.postman_collection.json`
- **Deployment Guide**: See `DEPLOYMENT_GUIDE.md`
- **Arabic Documentation**: See `README_AR.md`

## 🔗 Available Endpoints

### Authentication
- `POST /api/auth/register` - Register new user
- `POST /api/auth/login` - Login
- `POST /api/auth/logout` - Logout
- `GET /api/auth/user` - Get current user

### Posts
- `GET /api/posts` - List posts
- `POST /api/posts` - Create post
- `GET /api/posts/{id}` - Get post
- `PUT /api/posts/{id}` - Update post
- `DELETE /api/posts/{id}` - Delete post

### Analytics
- `GET /api/analytics/dashboard` - Dashboard stats
- `GET /api/analytics/posts/{id}` - Post analytics
- `GET /api/analytics/accounts/{id}` - Account analytics
- `GET /api/analytics/trends` - Engagement trends
- `GET /api/analytics/best-times` - Best posting times

### AI Content
- `POST /api/ai/generate-caption` - Generate caption
- `POST /api/ai/generate-hashtags` - Generate hashtags
- `POST /api/ai/improve-content` - Improve content
- `POST /api/ai/generate-ideas` - Generate ideas

### Brand Kits
- `GET /api/brand-kits` - List brand kits
- `POST /api/brand-kits` - Create brand kit
- `GET /api/brand-kits/{id}` - Get brand kit
- `PUT /api/brand-kits/{id}` - Update brand kit
- `DELETE /api/brand-kits/{id}` - Delete brand kit

### Social Accounts
- `GET /api/social-accounts` - List accounts
- `POST /api/social-accounts` - Connect account
- `PUT /api/social-accounts/{id}` - Update account
- `DELETE /api/social-accounts/{id}` - Delete account

### Subscriptions
- `GET /api/subscription-plans` - List plans (public)
- `POST /api/subscriptions/subscribe` - Subscribe
- `GET /api/subscriptions/current` - Current subscription
- `POST /api/subscriptions/cancel` - Cancel subscription

### Payments
- `GET /api/payments` - List payments
- `POST /api/payments` - Create payment
- `GET /api/payments/{id}` - Get payment

## 💡 Tips

### Use Postman
Import the Postman collection for easy testing:
1. Open Postman
2. Import `Media_Pro_API.postman_collection.json`
3. Set environment variable `base_url` = `http://127.0.0.1:8000`
4. Start testing!

### Common Commands

```bash
# Clear all cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Reset database
php artisan migrate:fresh --seed

# Run tests
php artisan test

# Check routes
php artisan route:list

# Start queue worker
php artisan queue:work
```

## 🐛 Troubleshooting

### Permission Issues
```bash
chmod -R 775 storage bootstrap/cache
```

### Database Connection Error
```bash
# Check database exists
php artisan tinker
DB::connection()->getPdo();
```

### Port Already in Use
```bash
# Use different port
php artisan serve --port=8001
```

## 🔒 Security Note

⚠️ **Important**: Change the default admin password immediately!

```bash
# Login to dashboard and update password
# Or use tinker:
php artisan tinker
$user = User::where('email', 'admin@admin.com')->first();
$user->password = bcrypt('your_new_secure_password');
$user->save();
```

## 📞 Need Help?

- Full Documentation: `README_AR.md`
- API Docs: `API_DOCUMENTATION.md`
- Deployment: `DEPLOYMENT_GUIDE.md`
- Email: support@mediapro.com

---

**Happy Coding! 🎉**
