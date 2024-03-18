# UserListing Block - knowledge test

This contains a Drupal module that integrates with a third-party API to fetch and display dummyusers data in a block. The module provides functionality to list users retrieved from the Reqres.in dummy API in a paginated format, displaying their email address, forename, and surname. The labels of these fields are customisable within the block and the users are created as **dummyuser** content entity instances as soon as the block is placed in the region through an EventSubscriber. The dummy users have a *status* flag which excludes them from the block listing the users.

*NOTE: The purpose of this Drupal module is to test my Drupal knowledge but could serve as an example of implementing a pager in a custom drupal block or more.*

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
  - Include unit tests in the package
  - May include integration and/or API tests.

# Setup & Steps

1. Install this in a fully functional Drupal environment using composer:

       $composer require drupal/userlisting
2. Enable the module:

       $drush en userlisting
 3. Go to *"/admin/structure/block"* and place the Custom "User List" block, preferably in the "Content Below" area as the block only contains "dummy" styling.
 4. On placement, the block form contains the **customisation of the labels**, as well as the **number of users shown per page**. 
 
*IMPORTANT: When the button "Save" is pressed the users are fetched and inserted in our custom DummyUser content entity.*

5. Now, check out the result on the page where the block was placed.
6. Further, the users locally have a **"status"** flag that allows the admin to filter through the list of users, which will be reflected in the block listing.
7. To alter that, go to *"/admin/content/dummyuser"* and press the button *"edit"*. Toggle the status checkbox and notice upon refresh the block is updated accordingly.
