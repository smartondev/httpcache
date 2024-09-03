# `ETagHeaderBuilder` class

The `ETagHeaderBuilder` class is used to build the `ETag` header.

The `ETag` header is a string that uniquely identifies a specific version of a resource. It is used to determine if the
resource has changed since the last request.

## Usage

### Week ETag

Generate a week ETag. The ETag will be a hash of the content and not represent the real version of the resource.

```php
$headers = (new ETagHeaderBuilder())
    ->withEtag('123456')
    ->withWeekEtag()
    ->toHeaders();
```

### ETag

Generate a strong ETag. The ETag will be a hash of the content and represent the real version of the resource.

```php
$headers = (new ETagHeaderBuilder())
    ->etag('123456')
    ->toHeaders();
```

### ETag from content

Generate a strong ETag from the content with a specific hash algorithm.

```php
$headers = (new ETagHeaderBuilder())
    ->computedETag($content, 'md5')
    ->toHeaders();
```