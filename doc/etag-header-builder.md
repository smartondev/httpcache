# `ETagHeaderBuilder` class

The `ETagHeaderBuilder` class is used to build the `ETag` header.

The `ETag` header is a string that uniquely identifies a specific version of a resource. It is used to determine if the
resource has changed since the last request.

## Usage

### Setting the ETag

You can set the ETag value directly.

#### Strong ETag

Generate a strong ETag. The ETag will be a hash of the content and represent the real version of the resource.

```php
$headers = (new ETagHeaderBuilder())
    ->etag('123456')
    ->toHeaders();
```

#### Weak ETag

Generate a weak ETag. The ETag will be a hash of the content and not represent the real version of the resource.

```php
$headers = (new ETagHeaderBuilder())
    ->etag('123456', true)
    ->toHeaders();

// or using the weekETag method
$headers = (new ETagHeaderBuilder())
    ->etag('123456')
    ->weekETag()
    ->toHeaders();
```

### ETag from content

Generate a strong ETag from the content with a specific hash algorithm.

```php
$headers = (new ETagHeaderBuilder())
    ->computedETag($content, fn($data) => md5($data))
    ->toHeaders();

// or using a string function name (since 'md5' is callable in PHP)
$headers = (new ETagHeaderBuilder())
    ->computedETag($content, 'md5')
    ->toHeaders();
```

### Immutable setters

The class also provides immutable setters, which return a new instance of the builder.

#### `withEtag()`

```php
$builder = new ETagHeaderBuilder();
$newBuilder = $builder->withEtag('123456');
```

#### `withComputedETag()`

```php
$builder = new ETagHeaderBuilder();
$newBuilder = $builder->withComputedETag($content, 'md5');
```

#### `withWeekEtag()`

```php
$builder = new ETagHeaderBuilder();
$newBuilder = $builder->withEtag('123456')->withWeekEtag();
```

### Resetting the ETag

You can reset the ETag value.

#### `resetETag()`

```php
$headers = (new ETagHeaderBuilder())
    ->etag('123456')
    ->resetETag()
    ->toHeaders(); // returns []
```

#### `withoutETag()`

This method returns a new instance without the ETag.

```php
$builder = (new ETagHeaderBuilder())->etag('123456');
$newBuilder = $builder->withoutETag();
```

### Resetting the weak flag

You can reset the weak flag to make the ETag strong.

#### `resetWeekETag()`

```php
$headers = (new ETagHeaderBuilder())
    ->etag('123456', true)
    ->resetWeekETag()
    ->toHeaders(); // ETag will be strong
```

#### `withoutWeekETag()`

This method returns a new instance with a strong ETag.

```php
$builder = (new ETagHeaderBuilder())->etag('123456', true);
$newBuilder = $builder->withoutWeekETag();
```

### Getting the header array

The `toHeaders()` method returns an array with the `ETag` header.

```php
$headers = (new ETagHeaderBuilder())
    ->etag('123456')
    ->toHeaders();

// ['etag' => '"123456"']
```

### Getting the ETag string

The `getETag()` method returns the ETag string.

```php
$etag = (new ETagHeaderBuilder())
    ->etag('123456')
    ->getETag();

// '"123456"'
```

The class also has a `__toString()` method, so you can cast the builder to a string.

```php
$etag = (string)(new ETagHeaderBuilder())->etag('123456');

// '"123456"'
```

### Checking if the ETag is set

You can check if the ETag is set using `isEmpty()` and `isNotEmpty()`.

```php
$builder = new ETagHeaderBuilder();
$builder->isEmpty(); // true
$builder->isNotEmpty(); // false

$builder->etag('123456');
$builder->isEmpty(); // false
$builder->isNotEmpty(); // true
```

## Related

- [ETag Matcher](./etag-matcher.md)

*This documentation was AI-generated.*
