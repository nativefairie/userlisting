# PLENTIFIC - knwoledge test

This repository contains a Drupal module that integrates with a third-party API to fetch and display user data in a block. The module provides functionality to list users retrieved from the Reqres.in dummy API in a paginated format, displaying their email address, forename, and surname. The labels of these fields are customisable within the block and the users are created as **dummyuser** content entity instances as soon as the block is placed in the region.

## Task

- Create a Drupal module as a Composer package
- Integrate with the  [https://reqres.in/](https://reqres.in/)  dummy API
- Provide a block listing the users from the third party service
- Display email address, forename, and surname of users in the block
- Support configuration for:
  -   Number of items per page
  -   Email field label
  -   Forename field label
  -   Surname field label Expose an extension point for consumer to filter the list of users
-  Include unit tests in the package
- May include integration and/or API tests.

# Setup & Steps


