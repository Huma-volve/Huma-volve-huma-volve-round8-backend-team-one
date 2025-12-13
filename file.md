# Adapter Design Pattern - الدليل الشامل 🔌

## المقدمة - إيه الحكاية؟

تخيل إنك راجع من سفر من أمريكا ومعاك شاحن laptop بس الفيشة American (قطبين مسطحين)، وعايز توصله بالكهربا في مصر (3 فتحات دائرية). هنا محتاج **محول/أدابتر** يحول من الفيشة الأمريكاني للمصري.

الـ **Adapter Pattern** بيعمل نفس الفكرة بالظبط في البرمجة - **بيحول interface من نظام قديم أو خارجي لـ interface تاني متوافق مع نظامك**.

---

## What is Adapter Pattern? | إيه هو الـ Adapter Pattern؟

الـ **Adapter Pattern** هو **Structural Design Pattern** بيسمح لـ objects ذات interfaces غير متوافقة إنها تشتغل مع بعضها. بيعمل زي "wrapper" أو "translator" بين two incompatible interfaces.

### التعريف العلمي

> "Convert the interface of a class into another interface clients expect. Adapter lets classes work together that couldn't otherwise because of incompatible interfaces."
>
> **بالعربي:** حوّل interface الـ class لـ interface تاني العميل بيتوقعه. الـ Adapter بيخلي classes تشتغل مع بعضها رغم إن interfaces بتاعتهم غير متوافقة.

---

## Problem & Solution | المشكلة والحل

### المشكلة 🔴

عندك نظام شغال، وعايز تستخدم **مكتبة خارجية** أو **legacy code** بس الـ interface بتاعها مختلف تماماً عن اللي نظامك بيتوقعه.

**مثال واقعي:**

- نظامك بيستخدم XML
- المكتبة الجديدة بتشتغل بـ JSON
- مش عايز تعدل كل الكود القديم

### الحل ✅

**Adapter Class** بتقف في النص:

- بتنفذ الـ interface اللي نظامك بيفهمها (Target Interface)
- جواها بتستخدم الـ object القديم/الخارجي (Adaptee)
- بتحول الـ calls من interface لـ interface

---

## Structure | البنية الأساسية

### Components المكونات

1. **Target Interface** - الـ interface اللي العميل بيتوقعها
2. **Client** - الكود اللي بيستخدم الـ Target Interface
3. **Adaptee** - الـ class القديمة/الخارجية اللي محتاجين نحولها
4. **Adapter** - الـ class اللي بتحول من Adaptee لـ Target

### UML Diagram

```
┌──────────────┐
│    Client    │
└──────┬───────┘
       │ uses
       ▼
┌──────────────────┐
│ Target Interface │
└──────────────────┘
       △
       │ implements
       │
┌──────┴───────┐
│   Adapter    │───────uses─────┐
└──────────────┘                │
                                ▼
                        ┌──────────────┐
                        │   Adaptee    │
                        └──────────────┘
```

---

## Types of Adapter | أنواع الـ Adapter

### 1. Object Adapter (الشائع)

بيستخدم **Composition** - الـ Adapter فيه instance من الـ Adaptee

### 2. Class Adapter

بيستخدم **Inheritance** - الـ Adapter بيرث من الـ Adaptee والـ Target
_(مش شائع في PHP لأنها بتدعم single inheritance بس)_

---

## Real-World Scenarios | سيناريوهات من الواقع 🌍

### Scenario 1: Payment Gateway Integration 💳

**المشكلة:**

- نظامك بيستخدم interface موحد للدفع
- كل payment gateway (Stripe, PayPal, Fawry) ليه API مختلف تماماً
- مش عايز تعدل كل الكود لكل gateway

**الحل:**
استخدام Adapter لكل gateway عشان يطابق الـ interface الموحد

**Benefits:**

- نفس الكود بيشتغل مع أي gateway
- سهولة إضافة gateways جديدة
- الكود العميل مايعرفش تفاصيل كل gateway

### Scenario 2: Legacy System Integration 🏛️

**المشكلة:**

- عندك نظام قديم بيشتغل بـ XML
- النظام الجديد بيشتغل بـ JSON
- مش عايز تعيد كتابة النظام القديم

**الحل:**
Adapter يحول من JSON لـ XML والعكس

### Scenario 3: Third-Party Libraries 📚

**المشكلة:**

- عايز تستبدل مكتبة logging قديمة بجديدة
- الـ API مختلف تماماً
- عندك مئات الملفات بتستخدم المكتبة القديمة

**الحل:**
Adapter يطابق interface المكتبة الجديدة مع القديمة

### Scenario 4: Database Migration 🗄️

**المشكلة:**

- بتنقل من MySQL لـ MongoDB
- الـ queries مختلفة تماماً
- عايز transition سلس

**الحل:**
Adapter يترجم الـ SQL-like queries لـ MongoDB queries

---

## Code Examples | أمثلة عملية 💻

> **ملحوظة:** شوف الملفات:
>
> - `example1_payment_gateway_adapter.php` - Payment Gateway Integration
> - `example2_notification_adapter.php` - Notification System Integration

---

## Advantages & Disadvantages | المزايا والعيوب

### Advantages ✅

1. **Single Responsibility Principle**
   - فصل الـ data conversion عن business logic
2. **Open/Closed Principle**

   - تقدر تضيف adapters جديدة من غير تعديل كود موجود

3. **Flexibility المرونة**
   - سهولة استبدال third-party libraries
4. **Reusability إعادة الاستخدام**

   - استخدام existing classes بدون تعديل

5. **Testability**
   - سهولة عمل mock للـ adapters

### Disadvantages ❌

1. **Increased Complexity**

   - classes إضافية وطبقات abstraction

2. **Performance Overhead**

   - الـ translation ممكن يأثر على الأداء

3. **Over-engineering**
   - ممكن يكون مش محتاج في الحالات البسيطة

---

## When to Use | امتى تستخدمه؟

### استخدم Adapter Pattern لما: ✅

1. **عايز تستخدم existing class بس interface-ها مش متوافق**
2. **محتاج تدمج third-party library**
3. **عندك legacy code وعايز تستخدمه في نظام جديد**
4. **عايز تعمل wrapper حول incompatible classes**
5. **بتعمل migration بين systems مختلفة**

### لا تستخدمه لما: ❌

1. **الـ interfaces متشابهة أو بسيطة**
2. **تقدر تعدل المصدر الأصلي بسهولة**
3. **مافيش incompatibility حقيقية**
4. **الـ overhead مش مبرر**

---

## Interview Questions | أسئلة الإنترفيو 🎯

### Basic Level | المستوى الأساسي

#### Q1: What is Adapter Pattern? | إيه هو الـ Adapter Pattern؟

**Answer:**

الـ Adapter Pattern هو Structural Design Pattern بيسمح لـ objects ذات interfaces غير متوافقة إنها تشتغل مع بعضها. بيعمل زي "translator" أو "wrapper" بين two incompatible interfaces.

**Key Points:**

- Structural Pattern
- حل مشكلة interface incompatibility
- بيستخدم Composition أو Inheritance
- مابيغيرش الكود الأصلي

**Real-world analogy:**
زي محول الكهربا من الفيشة الأمريكاني للمصري - بيحول من format لـ format تاني.

---

#### Q2: What's the difference between Adapter and Facade? | الفرق بين Adapter و Facade؟

**Answer:**

| Aspect                | Adapter                                | Facade                              |
| --------------------- | -------------------------------------- | ----------------------------------- |
| **Purpose الهدف**     | تحويل interface لـ interface تاني      | تبسيط interface معقد                |
| **Number of Classes** | بيشتغل مع class واحدة غالباً           | بيشتغل مع subsystem كامل            |
| **Interface**         | بينفذ interface موجود                  | بيعمل interface جديد مبسط           |
| **Intent النية**      | يخلي incompatible classes تشتغل مع بعض | يخفي complexity                     |
| **Example**           | XML-to-JSON adapter                    | Database Facade للـ complex queries |

**مثال Adapter:**

```php
// عايز نحول من interface لـ interface تاني
$adapter = new StripeAdapter($stripeGateway);
$adapter->processPayment(100); // نفس الـ interface للكل
```

**مثال Facade:**

```php
// عايز نبسط operations معقدة
$facade = new PaymentFacade();
$facade->processOrder($order); // بيخفي تعقيدات كتير جواه
```

---

#### Q3: What are the components of Adapter Pattern? | إيه مكونات الـ Adapter؟

**Answer:**

**1. Target Interface**

```php
interface PaymentProcessorInterface {
    public function pay(float $amount): bool;
    public function refund(string $transactionId): bool;
}
```

**2. Adaptee (الـ class القديم/الخارجي)**

```php
class StripePaymentGateway {
    public function charge($cents) { /* ... */ }
    public function createRefund($chargeId) { /* ... */ }
}
```

**3. Adapter**

```php
class StripeAdapter implements PaymentProcessorInterface {
    private StripePaymentGateway $stripe;

    public function __construct(StripePaymentGateway $stripe) {
        $this->stripe = $stripe;
    }

    public function pay(float $amount): bool {
        // Convert dollars to cents
        return $this->stripe->charge($amount * 100);
    }

    public function refund(string $transactionId): bool {
        return $this->stripe->createRefund($transactionId);
    }
}
```

**4. Client**

```php
class PaymentService {
    public function processPayment(PaymentProcessorInterface $processor) {
        return $processor->pay(99.99);
    }
}
```

---

### Intermediate Level | المستوى المتوسط

#### Q4: How does Adapter Pattern support SOLID principles? | إزاي الـ Adapter بيدعم SOLID؟

**Answer:**

**1. Single Responsibility Principle (SRP)**

```php
// الـ Adapter مسؤول بس عن الـ interface conversion
class PayPalAdapter implements PaymentProcessorInterface {
    private PayPalClient $paypal;

    // مسؤولية واحدة: تحويل interface PaymentProcessor لـ PayPal API
    public function pay(float $amount): bool {
        return $this->paypal->createPayment([
            'amount' => $amount,
            'currency' => 'USD'
        ]);
    }
}
```

**2. Open/Closed Principle (OCP)**

```php
// مفتوح للتوسع - تقدر تضيف adapters جديدة
class FawryAdapter implements PaymentProcessorInterface { }
class VodafoneCashAdapter implements PaymentProcessorInterface { }

// مقفول للتعديل - الكود العميل مابيتغيرش
class CheckoutController {
    public function checkout(PaymentProcessorInterface $processor) {
        // شغال مع أي adapter جديد
        $processor->pay(100);
    }
}
```

**3. Liskov Substitution Principle (LSP)**

```php
// أي adapter يقدر يحل محل التاني
function processOrder(PaymentProcessorInterface $processor) {
    return $processor->pay(50); // شغال مع أي implementation
}

processOrder(new StripeAdapter($stripe));
processOrder(new PayPalAdapter($paypal));
processOrder(new FawryAdapter($fawry));
```

**4. Interface Segregation Principle (ISP)**

```php
// Adapters بتنفذ بس الـ interface اللي العميل محتاجها
interface PaymentProcessorInterface {
    public function pay(float $amount): bool;
}

interface RefundablePaymentProcessor extends PaymentProcessorInterface {
    public function refund(string $transactionId): bool;
}

// بعض الـ adapters بتنفذ refunds، بعضها لأ
class StripeAdapter implements RefundablePaymentProcessor { }
class CashAdapter implements PaymentProcessorInterface { } // بدون refund
```

**5. Dependency Inversion Principle (DIP)**

```php
// نعتمد على abstractions (interfaces) مش concrete classes
class OrderService {
    public function __construct(
        private PaymentProcessorInterface $paymentProcessor // Interface مش concrete adapter
    ) {}

    public function placeOrder(Order $order) {
        return $this->paymentProcessor->pay($order->getTotal());
    }
}
```

---

#### Q5: Object Adapter vs Class Adapter - إيه الفرق؟

**Answer:**

**Object Adapter (الشائع في PHP) - بيستخدم Composition:**

```php
// Object Adapter - بيحتوي على instance من الـ Adaptee
class StripeAdapter implements PaymentProcessorInterface {
    private StripeGateway $stripe; // Composition

    public function __construct(StripeGateway $stripe) {
        $this->stripe = $stripe;
    }

    public function pay(float $amount): bool {
        return $this->stripe->charge($amount * 100);
    }
}

// Usage
$stripe = new StripeGateway();
$adapter = new StripeAdapter($stripe); // Inject dependency
```

**Advantages:**

- ✅ More flexible - تقدر تحول multiple adaptees
- ✅ Runtime flexibility - تقدر تغير الـ adaptee
- ✅ Follows composition over inheritance

**Class Adapter - بيستخدم Inheritance:**

```php
// Class Adapter - بيرث من الـ Adaptee
class StripeAdapter extends StripeGateway implements PaymentProcessorInterface {

    public function pay(float $amount): bool {
        // Direct access to parent methods
        return $this->charge($amount * 100);
    }
}

// Usage
$adapter = new StripeAdapter(); // No injection needed
```

**Disadvantages:**

- ❌ Less flexible - مقيد بـ single inheritance في PHP
- ❌ Tightly coupled
- ❌ Can't adapt multiple classes

**في PHP بنستخدم Object Adapter لأن:**

1. PHP بتدعم single inheritance بس
2. More flexible وtestable
3. Better separation of concerns

---

### Advanced Level | المستوى المتقدم

#### Q6: How to implement Two-Way Adapter? | إزاي تعمل Adapter اتجاهين؟

**Answer:**

Two-Way Adapter بيسمح بـ conversion في الاتجاهين - من A لـ B ومن B لـ A.

```php
interface ModernLoggerInterface {
    public function log(string $level, string $message, array $context = []): void;
}

class LegacyLogger {
    public function logInfo(string $msg): void {
        echo "[INFO] $msg\n";
    }

    public function logError(string $msg): void {
        echo "[ERROR] $msg\n";
    }
}

class TwoWayLoggerAdapter implements ModernLoggerInterface {
    private LegacyLogger $legacyLogger;

    public function __construct(LegacyLogger $legacyLogger) {
        $this->legacyLogger = $legacyLogger;
    }

    // Direction 1: Modern -> Legacy
    public function log(string $level, string $message, array $context = []): void {
        $contextString = !empty($context) ? json_encode($context) : '';
        $fullMessage = $message . ($contextString ? " | Context: $contextString" : '');

        match(strtolower($level)) {
            'info', 'notice', 'debug' => $this->legacyLogger->logInfo($fullMessage),
            'error', 'critical', 'alert', 'emergency' => $this->legacyLogger->logError($fullMessage),
            default => $this->legacyLogger->logInfo($fullMessage)
        };
    }

    // Direction 2: Legacy -> Modern (wrapper methods)
    public function legacyInfo(string $msg): void {
        $this->log('info', $msg);
    }

    public function legacyError(string $msg): void {
        $this->log('error', $msg);
    }
}

// Usage في الاتجاهين
$legacy = new LegacyLogger();
$adapter = new TwoWayLoggerAdapter($legacy);

// Modern interface
$adapter->log('error', 'Something went wrong', ['user_id' => 123]);

// Legacy interface
$adapter->legacyInfo('User logged in');
```

**Use Cases:**

- Migration scenarios (بتنقل تدريجياً)
- Integration مع systems قديمة وجديدة في نفس الوقت
- Backward compatibility

---

#### Q7: Adapter Pattern with Dependency Injection Container | الـ Adapter مع DI

**Answer:**

```php
// Target Interface
interface NotificationServiceInterface {
    public function send(string $recipient, string $message): bool;
}

// Adaptees (Third-party services)
class TwilioSmsService {
    public function sendSms(string $to, string $body, array $options = []): array {
        // Twilio API call
        return ['status' => 'sent', 'sid' => 'SM123'];
    }
}

class SendGridEmailService {
    public function sendEmail(array $data): object {
        // SendGrid API call
        return (object)['message' => 'success', 'id' => 'SG456'];
    }
}

// Adapters
class TwilioAdapter implements NotificationServiceInterface {
    public function __construct(
        private TwilioSmsService $twilio,
        private LoggerInterface $logger
    ) {}

    public function send(string $recipient, string $message): bool {
        $this->logger->info("Sending SMS via Twilio to {$recipient}");

        $result = $this->twilio->sendSms($recipient, $message);

        return $result['status'] === 'sent';
    }
}

class SendGridAdapter implements NotificationServiceInterface {
    public function __construct(
        private SendGridEmailService $sendGrid,
        private LoggerInterface $logger,
        private string $fromEmail
    ) {}

    public function send(string $recipient, string $message): bool {
        $this->logger->info("Sending Email via SendGrid to {$recipient}");

        $result = $this->sendGrid->sendEmail([
            'to' => $recipient,
            'from' => $this->fromEmail,
            'subject' => 'Notification',
            'body' => $message
        ]);

        return $result->message === 'success';
    }
}

// Laravel Service Provider Registration
class NotificationServiceProvider extends ServiceProvider {
    public function register() {
        // Register Twilio Adapter
        $this->app->bind('notification.sms', function ($app) {
            return new TwilioAdapter(
                $app->make(TwilioSmsService::class),
                $app->make(LoggerInterface::class)
            );
        });

        // Register SendGrid Adapter
        $this->app->bind('notification.email', function ($app) {
            return new SendGridAdapter(
                $app->make(SendGridEmailService::class),
                $app->make(LoggerInterface::class),
                config('mail.from.address')
            );
        });

        // Default notification service
        $this->app->bind(NotificationServiceInterface::class, function ($app) {
            $channel = config('notification.default_channel', 'email');
            return $app->make("notification.{$channel}");
        });
    }
}

// Usage في Controller
class NotificationController {
    public function __construct(
        private NotificationServiceInterface $notifier // DI Container يحقن الـ adapter المناسب
    ) {}

    public function sendNotification(Request $request) {
        $sent = $this->notifier->send(
            $request->input('recipient'),
            $request->input('message')
        );

        return response()->json(['sent' => $sent]);
    }
}

// أو استخدام named bindings
class MultiChannelNotification {
    public function __construct(
        #[Inject('notification.sms')]
        private NotificationServiceInterface $smsNotifier,

        #[Inject('notification.email')]
        private NotificationServiceInterface $emailNotifier
    ) {}

    public function notify(User $user, string $message) {
        if ($user->prefers_sms) {
            $this->smsNotifier->send($user->phone, $message);
        } else {
            $this->emailNotifier->send($user->email, $message);
        }
    }
}
```

**Benefits:**

- ✅ Automatic dependency resolution
- ✅ Easy to swap implementations
- ✅ Configuration-driven
- ✅ Testability (easy mocking)

---

## Best Practices | أفضل الممارسات 🏆

### 1. ✅ Keep Adapter Thin | خلي الـ Adapter بسيط

```php
// Good ✅ - Adapter بس بيحول
class StripeAdapter implements PaymentProcessorInterface {
    public function __construct(private StripeGateway $stripe) {}

    public function pay(float $amount): bool {
        // فقط تحويل الـ interface
        return $this->stripe->charge($amount * 100);
    }
}

// Bad ❌ - Adapter بيعمل business logic
class StripeAdapter implements PaymentProcessorInterface {
    public function pay(float $amount): bool {
        // ❌ Validation should be elsewhere
        if ($amount < 0) {
            throw new InvalidArgumentException();
        }

        // ❌ Logging should be elsewhere
        Log::info("Processing payment: $amount");

        // ❌ Email notification should be elsewhere
        Mail::send(new PaymentReceipt());

        return $this->stripe->charge($amount * 100);
    }
}
```

### 2. ✅ Use Type Hinting | استخدم Type Declarations

```php
// Good ✅
class PayPalAdapter implements PaymentProcessorInterface {
    public function __construct(
        private PayPalClient $paypal,
        private LoggerInterface $logger
    ) {}

    public function pay(float $amount): bool {
        // ...
    }
}

// Bad ❌
class PayPalAdapter {
    private $paypal;

    public function pay($amount) {
        // ...
    }
}
```

### 3. ✅ Handle Exceptions Properly | عالج الأخطاء صح

```php
class StripeAdapter implements PaymentProcessorInterface {
    public function pay(float $amount): bool {
        try {
            return $this->stripe->charge($amount * 100);
        } catch (StripeException $e) {
            // Convert to domain exception
            throw new PaymentFailedException(
                "Payment failed: " . $e->getMessage(),
                $e->getCode(),
                $e
            );
        }
    }
}
```

### 4. ✅ Document Adaptations | وثّق التحويلات

```php
class CurrencyAdapter implements PaymentProcessorInterface {
    /**
     * Process payment
     *
     * Note: This adapter converts USD to cents for Stripe API
     *
     * @param float $amount Amount in USD (e.g., 99.99)
     * @return bool
     * @throws PaymentFailedException
     */
    public function pay(float $amount): bool {
        $amountInCents = (int)($amount * 100); // USD to cents conversion
        return $this->stripe->charge($amountInCents);
    }
}
```

### 5. ✅ Use Configuration | استخدم الـ Configuration

```php
// config/payments.php
return [
    'stripe' => [
        'adapter' => StripeAdapter::class,
        'api_key' => env('STRIPE_API_KEY'),
        'currency_multiplier' => 100, // dollars to cents
    ],
];

// Adapter
class ConfigurableStripeAdapter implements PaymentProcessorInterface {
    public function __construct(
        private StripeGateway $stripe,
        private array $config
    ) {}

    public function pay(float $amount): bool {
        $multiplier = $this->config['currency_multiplier'] ?? 100;
        return $this->stripe->charge($amount * $multiplier);
    }
}
```

---

## Common Mistakes | الأخطاء الشائعة ⚠️

### ❌ Mistake 1: Adapter Doing Too Much

```php
// Bad ❌
class ComplexAdapter implements PaymentProcessorInterface {
    public function pay(float $amount): bool {
        // ❌ Too much responsibility
        $user = Auth::user();
        $this->validateUser($user);
        $this->checkBalance($user, $amount);
        $this->applyDiscount($amount);
        $this->sendNotification($user);
        $this->logTransaction();

        return $this->stripe->charge($amount * 100);
    }
}

// Good ✅ - Single Responsibility
class SimpleAdapter implements PaymentProcessorInterface {
    public function pay(float $amount): bool {
        // فقط التحويل
        return $this->stripe->charge($amount * 100);
    }
}
```

### ❌ Mistake 2: Not Handling Adapter Exceptions

```php
// Bad ❌
class UnsafeAdapter implements PaymentProcessorInterface {
    public function pay(float $amount): bool {
        // ❌ Let third-party exceptions leak
        return $this->stripe->charge($amount * 100);
    }
}

// Good ✅
class SafeAdapter implements PaymentProcessorInterface {
    public function pay(float $amount): bool {
        try {
            return $this->stripe->charge($amount * 100);
        } catch (StripeApiException $e) {
            throw new PaymentException("Stripe error: " . $e->getMessage(), 0, $e);
        } catch (NetworkException $e) {
            throw new PaymentException("Network error", 0, $e);
        }
    }
}
```

### ❌ Mistake 3: Tight Coupling to Concrete Adapter

```php
// Bad ❌
class OrderService {
    public function checkout(Order $order) {
        $adapter = new StripeAdapter(new StripeGateway()); // ❌ Tight coupling
        $adapter->pay($order->total);
    }
}

// Good ✅
class OrderService {
    public function __construct(
        private PaymentProcessorInterface $paymentProcessor // ✅ Depend on interface
    ) {}

    public function checkout(Order $order) {
        $this->paymentProcessor->pay($order->total);
    }
}
```

---

## Summary | الملخص 📝

### Key Takeaways | النقاط الأساسية 🎯

1. **Adapter Pattern = Interface Converter**

   - بيحول interface غير متوافق لـ interface متوافق

2. **استخدمه مع Third-Party Libraries**

   - سهولة integration مع APIs خارجية

3. **Promotes Loose Coupling**

   - الكود العميل مايعرفش تفاصيل الـ implementation

4. **Follows SOLID Principles**

   - خاصة SRP, OCP, DIP

5. **Object Adapter أفضل من Class Adapter في PHP**
   - أكثر مرونة وأسهل في الـ testing

### When to Use ✅

- ✅ Integration مع third-party libraries
- ✅ Working with legacy code
- ✅ عندك incompatible interfaces
- ✅ محتاج تعمل wrapper حول external systems

### When NOT to Use ❌

- ❌ The interfaces متشابهة
- ❌ تقدر تعدل المصدر الأصلي
- ❌ Simple wrapper كافي
- ❌ الـ overhead مش مبرر

---

## Laravel Examples | أمثلة من Laravel 🔥

### Cache Adapter في Laravel

```php
// Laravel بتستخدم Adapter Pattern للـ Cache Drivers

// Target Interface
interface CacheInterface {
    public function get(string $key);
    public function put(string $key, $value, int $seconds);
}

// Adapters لـ drivers مختلفة
class RedisAdapter implements CacheInterface {
    public function __construct(private Redis $redis) {}

    public function get(string $key) {
        return $this->redis->get($key);
    }

    public function put(string $key, $value, int $seconds) {
        $this->redis->setex($key, $seconds, serialize($value));
    }
}

class FileAdapter implements CacheInterface {
    public function get(string $key) {
        // Read from file
    }

    public function put(string $key, $value, int $seconds) {
        // Write to file with expiration
    }
}

// Usage
Cache::driver('redis')->put('key', 'value', 3600);
Cache::driver('file')->put('key', 'value', 3600);
```

---

## Additional Resources | مصادر إضافية 📚

### Books

- "Design Patterns: Elements of Reusable Object-Oriented Software" (Gang of Four)
- "Head First Design Patterns"
- "Patterns of Enterprise Application Architecture" (Martin Fowler)

### Online

- Refactoring Guru - Adapter Pattern
- SourceMaking - Design Patterns
- PHP The Right Way - Design Patterns

---

**Created by:** Egyptian Software Engineer 🇪🇬  
**Date:** December 2024  
**License:** MIT

---

> 💡 **Final Tip:** الـ Adapter Pattern أداة قوية للـ integration، بس استخدمه بحكمة. لو الـ interfaces قريبة من بعض، ممكن wrapper بسيط يكفي. الهدف هو الـ maintainability مش الـ over-engineering!
