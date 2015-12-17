@products
Feature: Products
    In order to know and pick the products
    As a visitor
    I want to be able to browse products

    Background:
        Given there is default currency configured
          And there are following taxonomies defined:
            | code | name     |
            | RTX1 | Category |
          And taxonomy "Category" has following taxons:
            | Clothing[TX1] > T-Shirts[TX2]     |
            | Clothing[TX1] > PHP T-Shirts[TX3] |
            | Clothing[TX1] > Gloves[TX4]       |
          And there are following channels configured:
            | code   | name       | currencies | locales             | url          |
            | WEB-US | mystore.us | EUR, GBP   | en_US               |              |
            | WEB-EU | mystore.eu | USD        | en_GB, fr_FR, de_DE | localhost    |
          And the following products exist:
            | name             | price | taxons       | pricing calculator | calculator configuration |
            | Super T-Shirt    | 19.99 | T-Shirts     | channel_based      | WEB-EU:15.99             |
            | Black T-Shirt    | 18.99 | T-Shirts     |                    |                          |
            | Sylius Tee       | 12.99 | PHP T-Shirts |                    |                          |
            | Symfony T-Shirt  | 15.00 | PHP T-Shirts |                    |                          |
            | Doctrine T-Shirt | 15.00 | PHP T-Shirts |                    |                          |
          And channel "WEB-EU" has following configuration:
            | taxonomy |
            | Category |
          And channel "WEB-EU" has following products assigned:
            | product         |
            | Super T-Shirt   |
            | Symfony T-Shirt |
          And channel "WEB-US" has following products assigned:
            | product          |
            | Sylius Tee       |
            | Black T-Shirt    |
            | Doctrine T-Shirt |

    Scenario: Browsing products by taxon
        Given I am on the store homepage
        When I follow "T-Shirts"
        Then I should see there 1 products
        And I should see "Super T-Shirt"

    Scenario: Empty index of products
        Given there are no products
        And I am on the store homepage
        When I follow "Gloves"
        Then I should see "There are no products to display"

    Scenario: Accessing product page via title
        Given I am on the store homepage
        And I follow "PHP T-Shirts"
        When I click "Symfony T-Shirt"
        Then I should be on the product page for "Symfony T-Shirt"

    Scenario: Display only products for current channel
        Given I am on the store homepage
        Then I should see "Super T-Shirt"
        But I should not see "Black T-Shirt"

    Scenario: Display proper product price for specific channel
        Given I am on the store homepage
        Then I should see "Super T-shirt"
        And I should see "€15.99"
