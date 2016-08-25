@applying_promotion_rules
Feature: Receiving discount based on nth order
    In order to pay less while placing an order
    As a Visitor
    I want to receive a discount for my purchase

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "PHP T-Shirt" priced at "$100.00"
        And the store ships everywhere for free
        And the store allows paying offline

    @ui
    Scenario: Receiving a discount on an first order
        Given there is a promotion "1st order promotion"
        And it gives "$20.00" off customer's 1st order
        And I have product "PHP T-Shirt" in the cart
        When I complete addressing step with email "john.doe@example.com" and "United States" as shipping country
        Then my cart total should be "$80.00"
        And my discount should be "-$20.00"

    @ui
    Scenario: Receiving no discount on an order if it is not first order placed
        Given there is a promotion "1st order promotion"
        And it gives "$20.00" off customer's 1st order
        And the customer "john.doe@example.com" has already placed an order "#001"
        And I have product "PHP T-Shirt" in the cart
        When I complete addressing step with email "john.doe@example.com" and "United States" as shipping country
        Then my cart total should be "$100.00"
        And there should be no discount

    @ui
    Scenario: Receiving no discount on an order if I placed more than one order
        Given there is a promotion "2nd order promotion"
        And it gives "$10.00" off customer's 2nd order
        And I have product "PHP T-Shirt" in the cart
        When I complete addressing step with email "john.doe@example.com" and "United States" as shipping country
        Then my cart total should be "$100.00"
        And there should be no discount
