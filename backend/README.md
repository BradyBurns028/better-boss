<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

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

## GIT
This section outlines the Git workflow and coding standards used. These rules are in place to ensure code quality, traceability, and team collaboration.

### Environments to Branches

* Production ⇄ `master`
    * `master` is perpetually green and deployable.
    * Only approved changes land in `master`.
* UAT ⇄ `dev`
    * `dev` is a staging area for integration and user acceptance testing.
    * `dev` is deployable to UAT at any time.

Rule of thumb: If it’s on `master`, it’s production‑ready. If it’s on `dev`, it’s UAT‑ready.

### High-Level Workflow
1. Branch from `master` for all work (featuers, bugfixes, chores).
2. Open a PR to `dev` and complete UAT.
3. After UAT approval, open a PR form `dev` (or the specific branch) to `master` at our convenience (release when ready).
4. Deploy:
* `dev` → UAT
* `master` → Production

This ensures we can ship to production without dragging along unapproved work.

### Commit Messages
All commit messages must be:

* Clear, concise, and written in imperative tense (e.g., "Fix bug", not "Fixed" or "Fixes").
* Descriptive enough to explain *why* a change was made, not just *what* was changed.
* Structured as:

```
#[ISSUE_NUMBER] Short summary of the change

Longer explanation if needed:
- What was the problem?
- What was the solution?
- Any implications (e.g., breaking changes, refactoring)?
```

**Examples:**

```bash
#123 Fix incorrect total in invoice calculation

Corrected rounding logic when summing item subtotals. 
Impacts payment gateway integration.
```
### Branching
* All feature and bugfix branches must branch **from `master`**.
* The naming convention for branches:

  ```
  Issue[ISSUE_NUMBER]-short-descriptive-name
  ```

  **Examples:**

    * `Issue456-add-user-profile`
    * `Issue789-fix-invoice-calculation`

* Every branch **must be linked to a tracked issue** in the project board.
* **Hotfix branches** are the exception and are based on `master` ([see below](#hot-fix-branches)).

### Pull Requests
* **Target**: All development branches must be merged into `dev` via PR.
* **Restrictions**:

    * PRs into `dev` or `master` must be **reviewed and approved** by someone other than the branch author.
    * Direct commits to `dev` or `master` are prohibited.
* **PR Naming Convention**:

  ```
  #[ISSUE_NUMBER] – Summary of the feature or fix
  ```
* **PR Description Template**:

  ```markdown
  ## Issue
  Closes #[ISSUE_NUMBER]

  ## Description
  Brief overview of the changes and reasoning.

  ## Impact
  List affected areas or modules.

  ## Testing
  Describe testing steps or link to automated test results.
  ```

#### Why Branch from Master, PR to Dev

* Clean start: Branching from `master` means no unapproved code.
* UAT separation: `dev` holds only reviewed work ready for testing.
* Controlled releases: We choose when to promote `dev → master`.
* Hotfix safety: Urgent fixes land in `master` first, then flow to `dev` without conflict.
* Minimizes cherry picking.

### Issues
* **Every branch must relate to a GitHub/GitLab issue**.
* Issues should:

    * Clearly describe the expected outcome or problem.
    * Include acceptance criteria.
    * Be labeled (e.g., `bug`, `enhancement`, `hotfix`, `documentation`).

### Hot Fix Branches

* Created **from `master`** only.
* Used only for **critical production issues** affecting primary business operations.
* Must be prefixed with `HTF-`:

  ```
  HTF-#[ISSUE_NUMBER]-short-description
  ```

  **Example:** `HTF-#1001-fix-auth-crash`
* PR flow:

    1. Branch from `master`
    2. Fix and PR back into `master`
    3. Merge `master` back into `dev` to synchronize changes

#### Pull Request Expectations
For PRs into `dev` (UAT):
* CI green (build, unit/integration tests, lint, type checks)
* Linked issue/ticket
* Release notes entry (brief)
* DB migration plan + rollback plan
* Feature flag noted (if applicable)
* Screenshots or curl/Postman examples for API changes
* Associate the Issue with the PR

For PRs into `master` (Production):
* UAT sign‑off recorded
* No open criticals
* Changelog/Release notes ready
* Associate the Issue with the PR

Review etiquette:
* Review within one business day.
* Use suggestion mode for minor fixes; request changes for correctness.
* Author merges after approvals.

### Merge Strategy
* **Squash & Merge** for PRs into `dev` and `master`, to maintain a clean and linear history.
* **No merge commits** in history unless resolving long-lived branches (e.g., hotfix sync).

### Branch Deletions
Branches should be deleted after the associated issue is closed and the change has been merged into `master` (Production). This keeps the repository clean and avoids confusion.

Why this timing?
* Ensures the final, shipped code is traceable to the branch until production is live.
* Avoids losing context if a rollback/revert is needed before or during release.

Guidelines:
* Delete feature/bugfix branches once merged to `dev → master` and deployed.
* Delete hotfix branches after merge and back-sync.
* Release branches may be deleted after production promotion.
* Long-lived branches are discouraged; keep branches short-lived.

#### Restoring a deleted branch
* If tagged on release, recreate from the tag: `git checkout -b Issue123-fix v2.4.0`.
* Otherwise, recreate from the merge base or last commit hash (available in PR history): `git checkout -b Issue123 <commit-sha>`.

### Security Considerations
* **Secrets and credentials must never be committed** (use `.env` or secret managers).
* Use `.gitignore` rigorously.
* Sensitive branches (e.g., `master`, `production`) should be protected in the repository settings:
    * Require PR reviews
    * Block force pushes
    * Enforce status checks before merging

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
