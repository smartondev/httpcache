# `ModifiedMatcher` class

The `ModifiedMatcher` class is used to check if the request headers contain the `If-Modified-Since` header and if the
last modified date is greater than the date in the header.

Additionally, it can check if the request headers contain the `If-Unmodified-Since` header and if the last modified date
is less than or equal to the date in the header.

## Usage

### If-Modified-Since

Check if the resource has been modified since the date specified in the `If-Modified-Since` header.

...

### If-Unmodified-Since

Check if the resource has not been modified since the date specified in the `If-Unmodified-Since` header.

...

