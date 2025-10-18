# 🔌 قائمة API Endpoints - Media Pro

## 📱 تسجيل الدخول بالحسابات الاجتماعية

### Google OAuth

#### 1. Web Flow (للمواقع)
```http
# الخطوة 1: الحصول على رابط تسجيل الدخول
GET /api/auth/google

Response:
{
    "success": true,
    "data": {
        "url": "https://accounts.google.com/o/oauth2/v2/auth?..."
    }
}

# الخطوة 2: المستخدم يسجل الدخول ويُعاد توجيهه إلى callback
GET /api/auth/google/callback?code=...

Response:
{
    "success": true,
    "message": "تم تسجيل الدخول بنجاح",
    "data": {
        "user": { ... },
        "token": "1|xxxxxxxxxxxxx",
        "token_type": "Bearer"
    }
}
```

#### 2. Mobile Flow (لتطبيقات الموبايل)
```http
POST /api/auth/google/token
Content-Type: application/json

{
    "id_token": "eyJhbGciOiJSUzI1NiIs..."
}

Response:
{
    "success": true,
    "message": "Login successful",
    "data": {
        "user": {
            "id": 1,
            "name": "محمد أحمد",
            "email": "mohamed@gmail.com",
            "google_id": "12345678901234567890"
        },
        "token": "1|xxxxxxxxxxxxx",
        "token_type": "Bearer"
    }
}
```

---

### Apple OAuth

#### 1. Web Flow (للمواقع)
```http
# الخطوة 1: الحصول على رابط تسجيل الدخول
GET /api/auth/apple

Response:
{
    "success": true,
    "data": {
        "url": "https://appleid.apple.com/auth/authorize?..."
    }
}

# الخطوة 2: المستخدم يسجل الدخول ويُعاد توجيهه إلى callback
GET /api/auth/apple/callback?code=...

Response:
{
    "success": true,
    "message": "تم تسجيل الدخول بنجاح",
    "data": {
        "user": { ... },
        "token": "2|xxxxxxxxxxxxx",
        "token_type": "Bearer"
    }
}
```

#### 2. Mobile Flow (لتطبيقات الموبايل)
```http
POST /api/auth/apple/token
Content-Type: application/json

{
    "id_token": "eyJraWQiOiJXNldjT0t...",
    "user": "{\"name\":{\"firstName\":\"أحمد\",\"lastName\":\"محمد\"}}"  // فقط في أول تسجيل
}

Response:
{
    "success": true,
    "message": "Login successful",
    "data": {
        "user": {
            "id": 2,
            "name": "أحمد محمد",
            "email": "ahmed@privaterelay.appleid.com",
            "apple_id": "001234.abcdef1234567890.1234"
        },
        "token": "2|xxxxxxxxxxxxx",
        "token_type": "Bearer"
    }
}
```

---

## 🔐 Authentication Headers

بعد تسجيل الدخول، استخدم الـ token في جميع الطلبات المحمية:

```http
Authorization: Bearer 1|xxxxxxxxxxxxx
```

---

## 📊 مثال تكامل React Native / Flutter

### React Native (Google Sign In)
```javascript
import { GoogleSignin } from '@react-native-google-signin/google-signin';

// Configure
GoogleSignin.configure({
  webClientId: 'YOUR_GOOGLE_CLIENT_ID',
});

// Sign in
async function signInWithGoogle() {
  const { idToken } = await GoogleSignin.signIn();

  const response = await fetch('http://yourdomain.com/api/auth/google/token', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
    },
    body: JSON.stringify({ id_token: idToken }),
  });

  const data = await response.json();

  if (data.success) {
    // حفظ التوكن
    await AsyncStorage.setItem('token', data.data.token);
    // الانتقال للصفحة الرئيسية
    navigation.navigate('Home');
  }
}
```

### React Native (Apple Sign In)
```javascript
import { appleAuth } from '@invertase/react-native-apple-authentication';

async function signInWithApple() {
  const appleAuthRequestResponse = await appleAuth.performRequest({
    requestedOperation: appleAuth.Operation.LOGIN,
    requestedScopes: [appleAuth.Scope.EMAIL, appleAuth.Scope.FULL_NAME],
  });

  const { identityToken, fullName } = appleAuthRequestResponse;

  const response = await fetch('http://yourdomain.com/api/auth/apple/token', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
    },
    body: JSON.stringify({
      id_token: identityToken,
      user: fullName ? JSON.stringify({ name: fullName }) : undefined,
    }),
  });

  const data = await response.json();

  if (data.success) {
    await AsyncStorage.setItem('token', data.data.token);
    navigation.navigate('Home');
  }
}
```

### Flutter (Google Sign In)
```dart
import 'package:google_sign_in/google_sign_in.dart';
import 'package:http/http.dart' as http;

final GoogleSignIn _googleSignIn = GoogleSignIn(
  scopes: ['email'],
);

Future<void> signInWithGoogle() async {
  final GoogleSignInAccount? googleUser = await _googleSignIn.signIn();
  final GoogleSignInAuthentication googleAuth = await googleUser!.authentication;

  final response = await http.post(
    Uri.parse('http://yourdomain.com/api/auth/google/token'),
    headers: {'Content-Type': 'application/json'},
    body: jsonEncode({'id_token': googleAuth.idToken}),
  );

  final data = jsonDecode(response.body);

  if (data['success']) {
    // حفظ التوكن
    await storage.write(key: 'token', value: data['data']['token']);
    // الانتقال للصفحة الرئيسية
    Navigator.pushReplacementNamed(context, '/home');
  }
}
```

### Flutter (Apple Sign In)
```dart
import 'package:sign_in_with_apple/sign_in_with_apple.dart';
import 'package:http/http.dart' as http;

Future<void> signInWithApple() async {
  final credential = await SignInWithApple.getAppleIDCredential(
    scopes: [
      AppleIDAuthorizationScopes.email,
      AppleIDAuthorizationScopes.fullName,
    ],
  );

  final response = await http.post(
    Uri.parse('http://yourdomain.com/api/auth/apple/token'),
    headers: {'Content-Type': 'application/json'},
    body: jsonEncode({
      'id_token': credential.identityToken,
      'user': credential.givenName != null
        ? jsonEncode({
            'name': {
              'firstName': credential.givenName,
              'lastName': credential.familyName,
            }
          })
        : null,
    }),
  );

  final data = jsonDecode(response.body);

  if (data['success']) {
    await storage.write(key: 'token', value: data['data']['token']);
    Navigator.pushReplacementNamed(context, '/home');
  }
}
```

---

## 🧪 اختبار سريع بـ cURL

### Test Google Login
```bash
# سيعطيك رابط للتسجيل
curl http://localhost:8000/api/auth/google
```

### Test Apple Login
```bash
# سيعطيك رابط للتسجيل
curl http://localhost:8000/api/auth/apple
```

### Test Token Login (بعد الحصول على ID Token)
```bash
curl -X POST http://localhost:8000/api/auth/google/token \
  -H "Content-Type: application/json" \
  -d '{"id_token":"YOUR_GOOGLE_ID_TOKEN_HERE"}'
```

---

## ⚠️ ملاحظات مهمة

### 1. حول Google ID Token
- يمكن الحصول عليه من `@react-native-google-signin/google-signin`
- أو من `google-auth-library` للويب
- صالح لمدة ساعة واحدة فقط

### 2. حول Apple ID Token
- يأتي من `@invertase/react-native-apple-authentication`
- Apple ترسل معلومات المستخدم (name) فقط في أول مرة
- احفظ الاسم في التطبيق في أول تسجيل

### 3. الأمان
- **لا تخزن** ID Tokens في التطبيق
- استخدمها مباشرة للحصول على Bearer Token
- احفظ فقط Bearer Token الذي يأتي من الباك إند

### 4. معالجة الأخطاء
```javascript
try {
  const response = await fetch('http://yourdomain.com/api/auth/google/token', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ id_token: idToken }),
  });

  const data = await response.json();

  if (!data.success) {
    Alert.alert('خطأ', data.message);
    return;
  }

  // نجح التسجيل
  console.log('Token:', data.data.token);

} catch (error) {
  Alert.alert('خطأ', 'فشل الاتصال بالسيرفر');
  console.error(error);
}
```

---

## 📞 الدعم

إذا واجهتك أي مشاكل:
1. تحقق من أن المفاتيح في `.env` صحيحة
2. تأكد من أن التطبيق مُعدّ بشكل صحيح على Google/Apple Console
3. راجع الـ logs في `storage/logs/laravel.log`

---

**جاهز للاستخدام! 🚀**
