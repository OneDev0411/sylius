@customer_account
Feature: Viewing orders only from current channel
    In order to follow my orders
    As a Customer
    I want to be able to track my placed orders from current channel

    Background:
        Given the store operates on a channel named "Web-US" in "USD" currency
        And the store operates on a channel named "Web-UK" in "GBP" currency
        And the store has country "United States"
        And the store has country "United Kingdom"
        And the store has a product "Angel T-Shirt" priced at "$100" in "Web-US" channel
        And this product is also priced at "£200" in "Web-UK" channel
        And the store ships everywhere for free
        And the store allows paying with "Cash on Delivery"
        And there is a customer "John Hancock" identified by an email "hancock@superheronope.com" and a password "superPower"
        And this customer has placed an order "#00000001" buying a single "Angel T-Shirt" product for "$100" on the "Web-US" channel
        And the customer "John Hancock" addressed it to "350 5th Ave", "10118" "New York" in the "United States" with identical billing address
        And this customer has also placed an order "#00000004" buying a single "Angel T-Shirt" product for "£200" on the "Web-UK" channel
        And the customer "Sherlock Holmes" addressed it to "221B Baker Street", "44123" "London" in the "United Kingdom" with identical billing address
        And I am logged in as "hancock@superheronope.com"

    @ui
    Scenario: Viewing orders only from current channel
        When I change my current channel to "Web-US"
        And I browse my orders
        Then I should see a single order in the list
        And this order should have "#00000001" number
