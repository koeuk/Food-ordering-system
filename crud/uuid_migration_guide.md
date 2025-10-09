# UUID Migration Guide: From dyrynda/laravel-model-uuid to Laravel Native UUIDs

## Overview
This guide explains how to migrate from `dyrynda/laravel-model-uuid` to Laravel's native UUID support.

## Key Changes

### 1. Updated HasUuidTrait
The new `HasUuidTrait` now uses Laravel's built-in `HasUuids` trait and provides:
- Automatic UUID generation for the `uuid` column
- Route model binding via UUID
- `findByUuidOrFail()` method for backward compatibility
- `whereUuid()` scope for backward compatibility

### 2. UUID Version
- Laravel 12 uses UUID v7 by default (time-ordered, better for database indexing)
- If you need UUID v4 (random), update your trait to use:
  ```php
  use Illuminate\Database\Eloquent\Concerns\HasVersion4Uuids as HasUuids;
  ```

### 3. Route Binding
Routes automatically bind via UUID:
```php
// This works automatically
Route::get('/users/{user}', function (User $user) {
    return $user;
});
```

### 4. Database Considerations
- Ensure your UUID columns are `CHAR(36)` or `UUID` type
- UUID v7 provides better indexing performance due to time-ordering

## Migration Steps

1. **Remove the old package:**
   ```bash
   composer remove dyrynda/laravel-model-uuid
   ```

2. **Update your models (if needed):**
   - If using a non-standard UUID column name, override `uniqueIds()` in your model
   - If you don't want route binding via UUID, override `getRouteKeyName()`

3. **Test your application:**
   - Test route model binding
   - Test UUID generation for new models
   - Test `findByUuidOrFail()` calls
   - Test `whereUuid()` scope usage

## Benefits of Native Laravel UUIDs

1. **No external dependencies** - One less package to maintain
2. **Better performance** - UUID v7 is time-ordered, improving database indexing
3. **Native integration** - Built into Laravel's core
4. **Future-proof** - Will be maintained as part of Laravel

## Customization Options

### Using a different UUID column name:
```php
public function uniqueIds(): array
{
    return ['custom_uuid_column'];
}
```

### Disabling automatic UUID generation:
```php
public function uniqueIds(): array
{
    return [];
}
```

### Custom UUID generation logic:
```php
public function newUniqueId(): string
{
    return Str::uuid()->toString();
}
```

## Troubleshooting

1. **Route not found errors:**
   - Ensure your routes are using the model instance, not `{id}`
   - Check that `getRouteKeyName()` returns 'uuid'

2. **UUID not generating:**
   - Verify the column exists in your database
   - Check that `uniqueIds()` includes your UUID column

3. **Type errors:**
   - Ensure your UUID column is nullable in the database if creating records without UUIDs