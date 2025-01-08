# Changelog

## dev

# 0.6.0

- enh: custom DateMalFormedStringException added
- fix: ETagHeaderBuilder#etag() etag set and already set with null -> null etag not stored 
- rename: ETagMatcher#ifMatchHeader() -> ETagMatcher#ifMatchHeaderValue()
- rename: ETagMatcher#withIfMatchHeader() -> ETagMatcher#withIfMatchHeaderValue()

## 0.5.0

- fix: ModifiedMatcher::ifModifiedSinceHeader(), ::withIfModifiedSinceHeader(), ::ifUnmodifiedSinceHeader(),
  ::withIfUnmodifiedSinceHeader() invalid header value addition
- fix: ETagMatcher::ifMatchHeader(), ::withIfMatchHeader(), ::ifNoneMatchHeader(), ::withIfNoneMatchHeader() invalid
  header value addition
- remove: ETagMatcherResult::matches() removed (BC break)

## 0.4.0

- enh: CacheHeaderBuilder::withoutMustRevalidate() added
- refactor: ETagHeaderBuilder::resetETagWeek() -> resetWeekETag() (BC break)

## 0.3.1

- fix: wrong class name CacheHeaderBuilderBuilder -> CacheHeaderBuilder
- fix: wrong class name ETagHeaderBuilderBuilder -> ETagHeaderBuilder

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