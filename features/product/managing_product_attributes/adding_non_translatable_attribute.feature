@managing_product_attributes
Feature: Adding a new non translatable product attribute
    In order to add non translatable attribute
    As an Administrator
    I want to be able to toggle translatable checkbox

    Background:
        Given the store is available in "English (United States)"
        And I am logged in as an administrator

    @ui
    Scenario: Adding a new non translatable product attribute
        Given I want to create a new integer product attribute
        When I specify its code as "damage"
        And I name it "Sword" in "English (United States)"
        And i disable its translatable
        And I add it
        Then I should be notified that it has been successfully created
