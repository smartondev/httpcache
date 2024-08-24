# Changelog

## dev



## 0.3.0

- fix: CacheHeaderBuilder::reset() lastModified reset
- refactor: namespace refactoring (BC break)

## 0.2.1

- fix: no-cache reset fix (private, public, noStore, mustRevalidate) #2

## 0.2.0

- enh: mutable accessors
- enh: no-cache with more control tags + pragma
- fix: no-cache reset if not used
- enh: more tests added
- enh: cache control tags sorted by name
- enh: lowercased header names
- fix: CacheHeaderBuilder::reset() staleWhileRevalidate and staleIfError
- enh: header always converted to lowercase
- enh: CacheHeaderBuilder::hasLastModified(), hasEtag(), isNoCache(), isEmpty(), isNotEmpty(), getEtag()
- remove: ModifiedMatcherResult:: isBeforeModifiedSince, isAfterModifiedSince, isBeforeModifiedAt, isAfterModifiedAt,
  isEqualsModifiedAt, isEqualsUnmodifiedSince
- enh: ModifiedMatcherResult::isModifiedSince, matchesModifiedAt, isUnmodifiedSince
- fix: empty ETAG use as NO ETAG

## 0.1.0

Initial release