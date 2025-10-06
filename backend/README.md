# Better Boss Backend README

## Table of Contents

1. [About Laravel](#about-laravel)
2. [Learning Laravel](#learning-laravel)
3. [Coding Standards](#coding-standards)
    - [PHP Conventions](#php-conventions)

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Coding Standards

The following are the coding standards for the backend project.

### PHP Conventions

#### Variables
Always use descriptive variable and constant names.

The only time a single letter is acceptable as a variable is within a for loop. The highest level loop should always use the variable `$i` (for iterator). Each nested loop under that should increment the letter by one (e.g. j, k, l...). This is a shorthand for tracking nesting of for loop, so `$k` would be iterator + 2 levels of nesting.

```php
for ($i = 0; $i < 10; $i++>) {
    for ($j = 0; $j < 15; $j++>) {
        echo $my_multi_dimensional_array[$i][$j];
    }
}
```

#### Case Conventions
The following is the expected naming standards for PHP syntax

| Type | Standard | Example |
| --------------------- | ------------------------- | --------------------------------------- |
| Classes | PascalCase | MyClass, AnotherClass |
| Methods | camelCase | myMethod, anotherMethod |
| Constants | UPPER_SNAKE_CASE | MY_CONSTANT, ANOTHER_CONSTANT |
| Variables | snake_case or camelCase  | `$my_variable, $myVariable` |
| File Names | PascalCase  |MyFile.php, AnotherFile.php |
| Namespaces | Typically reflect the file structure, with the first part capitalized | Vendor\Package\MyClass |

### SQL Conventions
The following is the expected naming standard when building raw SQL in the PHP Code.

| Type | Standard |Example |
| ---- | ------ | ------ |
| Keywords | UPPER | SELECT, WHERE FROM |
| User Tables | camelCase or snake_case **DO NOT USE \" in names**. MyTable and mytable are the same, while "MyTable" and "mytable" are not. User Table names should always be singular nouns. | store, buyPlan buy_plan_month|
| User Functions, Stored Procedures | cameClase or SnakeCase. Should be a verb phrase | getStores,  saveStore|
| View | cameClase or SnakeCase. Should be a plural noun | stores, buyPlans |

### Commenting

Code Comments are expected. In general, too many comments is preferred over to few.  The primary expectation is that a comment will describe the reasoning behind the way the logic is structured.

#### Method Header Comments

A Method Comment is a multi-line comment at the start of a method which describes the inputs and outputs for the method. The example below shows a expected method header. Note, if a element isn't used (i.e. there's no Return for the method), it is acceptable to leave it off. This does mean that it's possible that a Method Header comment will just contain the reason for the method if it does not accpet inputs, does not have outputs, and does not generate special exceptions.

```php

/* ***********************************************
* function name: sendEmail
*       Sends Email to the provided address, 
*           containing boilerplate and the provided value
* Inputs:
*       $email - String, the email that function sends value to
*       $value - Integer | String. The value being sent to the email
* Return:
* Exceptions: 
 *********************************************** */
public function sendEmail(string $email, $value)
   {
    // ...
   }

```

## Testing
Before diving into specifics, it’s important to define why we test.
* Tests are not just safety nets; they’re documentation, contracts, and guardrails.
* The testing suite should enable rapid refactoring and fearless deployment.
* PestPHP is chosen not for novelty, but for its expressiveness, light syntax, and developer ergonomics.
### Running Tests
1. Default Laravel Test Command
    ```bash
    php artisan test
    ```
2. Run a Specific Test File
    ```bash
    php artisan test tests/Feature/Auth/LoginTest.php
    ```
3. Run a specific Test Method
   ```bash
   php artisan test --filter=test_user_can_login
   ```
4. Prepare the Database Before Running Tests
    * Laravel's test often depend on a fresh datbase.
    * Ensure you're point to `.env.testing` and using a Clean DB
         ```bash
         php artisan config:clear
         php artisan migrate:fresh --env=testing
         php artisan test
         ```
    * Or for quick one-liner runs
         ```bash
         php artisan migrate:fresh --env=testing && php artisan test
         ```

### Test Types
| Test Type      |  Purpose   | Tooling / Scope  |
| ---------- | ----------------- | -------------------- |
| Unit Tests | Validate isolated functions/classes | `tests/Unit` with PestPHP |
| Feature Tests | Test HTTP requests, controllers, and middleware logic | `tests/Feature` |
| Integration Tests | Validate system boundaries and service coordination | Optional subdir: `tests/Integration` |
| End-to-End (E2E)  | Browser testing, full-stack flow | Laravel Dusk (optional layer) |
| Performance Tests | Stress, load, and benchmark-specific logic | Optional tooling layer outside Pest |

### Test Naming and Structure
* Describe behavior, not implementation
    ```PHP
    it('displays a 404 if the post does not exist', function () {
        $response = $this->get('/posts/non-existent');
        $response->assertNotFound();
    });
    ```
* Organize by domain and concern:
    ```
    test/
        Unit/
            Services/
            Helpers/
        Feature/
            Http/
            Auth/
            Posts/
    ```
### Writing Standards
✅ Do:
* Use factories and database transactions to isolate test state.
* Rely on Pest’s higher-order syntax and closures for clarity.
* Group similar test cases using describe() and dataset().

🚫 Don’t:
* Avoid mocking Laravel’s internal components unless absolutely necessary.
* Avoid writing redundant tests (e.g., testing Laravel's built-in validation logic directly).

  **Example:**
    ```PHP
    use App\Models\Post;

    it('allows a user to view a published post', function () {
        $post = Post::factory()->create(['published' => true]);

        $this->get("/posts/{$post->id}")
            ->assertOk()
            ->assertSee($post->title);
    });
    ```

### Setup and Teardown
* Use RefreshDatabase trait for database-related tests. ***NOTE:** This will be related to updating to use Database Seeding Functionality*
* If performance becomes an issue, migrate once per test suite (DatabaseMigrations), not per test.
* Configure .env.testing for isolated configuration (e.g., sqlite in-memory DB).

### Continuous Integration Requirements
Every pull request must:
* Pass all test suites
* Be blocked if code coverage drops below threshold (e.g., 85%)
* Lint tests with PHP-CS-Fixer or Pint

Example GitHub Action step:
```yaml
- name: Run Pest Tests
  run: |
    cp .env.testing .env
    php artisan config:clear
    php artisan migrate --env=testing --force
    ./vendor/bin/pest
```

### Testing New Features
Every new feature must come with:
* At least one happy path feature test
* One or more edge case tests
* Any relevant unit tests for services/helpers

### Security Testing
* Include tests for:
    * Authorization gates
    * Role-based access
    * Input sanitization
* Intentionally write tests for unauthorized scenarios:
    ```PHP
    it('prevents guests from deleting posts', function () {
        $post = Post::factory()->create();
        $this->delete("/posts/{$post->id}")->assertRedirect('/login');
    });
    ```
### Legacy and Technical Debt Coverage
* Gradually backfill tests during related development (Boy Scout Rule).
