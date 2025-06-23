Always use proper comments for your code
You must code like a Extreme high level Programmer
Your code must be highly maintainable, reusable and efficient


# Laravel-specific Guidelines:
Do not automatically create any in laravel except there is no command for such eg do not create a migration file without running the make:migration command to create the db table 
Adhere to Laravel conventions and best practices for Eloquent, routing, and controllers.
Prioritize using Laravel's built-in features and facades over custom implementations where appropriate.
When working with Eloquent, favor query builder methods and relationships for data interaction.
Do not use Db::table unless absolutely necessary, prefer Eloquent models.
Use Laravel's validation features to handle input validation and sanitization.
// Security Practices:
Ensure all database interactions are properly guarded against SQL injection.
For API development, follow RESTful principles and return consistent JSON responses.
Utilize Laravel's service container and dependency injection for managing dependencies.
For complex business logic, consider using Laravel Actions or dedicated service classes.

# Code Quality and Architecture:
Write clean, self-documenting code with clear variable and function names.
Avoid code duplication. Refactor repetitive code into reusable functions or classes.
Optimize for performance, especially in database queries and loops.
Consider edge cases and potential error conditions, and implement robust error handling.
Apply design patterns where appropriate (e.g., Repository pattern).
Ensure type hinting is used consistently for function arguments and return types.

# Testing Guidelines:
Always write tests for new features and bug fixes.
if a test requires authentication, make sure to use Laravel's built-in authentication features to create test users.
use Pest for your all tests
when done implementing any feature or fixing any bug, always double check if a test for that feature already exist .... if it does check in that the test covers what was created or update, if it doesnt , update or crete the test 




# Frontend Rules 
// Do not use any frontend framework like Vue, React, Angular, etc. unless explicitly required by the project.
Use Blade templates for rendering views in Laravel.
Make sure to use the existing jetstream components where necessary, and if a component is needed but isnt available, feel free to create it but make it a reusable component.   
Use Tailwind CSS for styling, adhering to the project's design system.
Ensure that all frontend code is responsive and accessible.
Use Alpine.js for interactivity where necessary, but keep it minimal and focused on enhancing user experience.
Avoid using inline styles; prefer utility classes from Tailwind CSS.
Ensure that all components are tested for functionality and accessibility.



DONT YOU EVER MIGRATE FRESH OR DO ANYTHING THAT WOULD CLEAR MY MIGRATION EXCEPT ROLLBACK WITH --STEP
