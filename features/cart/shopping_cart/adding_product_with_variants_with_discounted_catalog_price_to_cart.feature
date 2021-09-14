@shopping_cart
Feature: Adding a product with selected variant with discounted catalog price to the cart
    In order to select products with proper price
    As a Visitor
    I want to be able to add products with selected variants to cart

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a "T-Shirt" configurable product
        And this product has "PHP T-Shirt" variant priced at "$20"
        And this product has "Kotlin T-Shirt" variant priced at "$400"
        And the store has a "Keyboard" configurable product
        And this product has "RGB Keyboard" variant priced at "$40"
        And this product has "Pink Keyboard" variant priced at "$300"
        And there is a catalog promotion "Winter sale" that reduces price by "25%" and applies on "PHP T-Shirt" variant

    @ui
    Scenario: Adding multiple product variants with discounted price by catalog promotion catalog to the cart
        Given I add "PHP T-Shirt" variant of product "T-Shirt" to the cart
        And I add "RGB Keyboard" variant of product "Keyboard" to the cart
        When I check details of my cart
        Then I should see "T-Shirt" with unit price "$15.00" in my cart
        And I should see "Keyboard" with unit price "$40.00" in my cart
