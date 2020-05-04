# DTO Examples

## Table of contents
- [Basic usage](#basic-usage)
- [Collection of DTOs](#collection-of-dtos)
- [Collection of nested DTOs](#collection-of-nested-dtos)

## Basic usage

```bash
$ php examples/basic_usage.php
Post "sunt aut facere repellat..." (#1) was created by author 1
```

## Collection of DTOs

```bash
$ php examples/collection_of_dtos.php
...
Post #68 was created by author 7
Post #69 was created by author 7
Post #70 was created by author 7
Post #71 was created by author 8
...
```

## Collection of nested DTOs

```bash
$ php examples/nested_dtos.php
Author John Doe (#1)
Posts by the author:
-- #42: Lorem ipsum
-- #43: Dolor sit amet
--------------------
Author Jane Doe (#2)
Posts by the author:
-- #7: Dolor sit amet
-- #8: Lorem ipsums
--------------------
```