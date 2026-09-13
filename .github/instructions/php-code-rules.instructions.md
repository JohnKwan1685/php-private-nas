---
name: PHP code rules
description: PHP coding and database safety rules for this Laravel project.
applyTo: "**/*"
---

# PHP Code Rules

- When a PHP model uses `Illuminate\Support\Carbon` in PHPDoc, import it as `Date` with `use Illuminate\Support\Carbon as Date;`.
- Use `Date|null` for nullable Carbon date properties such as `created_at`, `updated_at`, and `email_verified_at`.
- Keep PHPDoc property annotations consistent with the corresponding database migration.
- Do not inspect, query, export, or display database records or other stored database data.
- Do not directly insert, update, delete, truncate, or otherwise modify database data.
- Database changes must be made as migration or schema code only; do not execute data-changing database commands unless the user explicitly authorizes it.
- Put reusable input validation in a dedicated Laravel `FormRequest` instead of duplicating validation rules in controllers.
- Let reusable request classes expose validated domain inputs, such as authentication credentials, so controllers only coordinate the workflow.
- Keep external API field names centralized in their request contract rather than scattering literal field names across controllers.
