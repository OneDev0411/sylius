@currencies
Feature: Handling disabled currencies
    In order to see right prices
    As a Customer
    I want to browse channels with a valid currency only

    Background:
        Given the store operates on a channel named "Web"
        And that channel allows to shop using "EUR", "USD" and "GBP" currencies
        And it uses the "EUR" currency by default
        But the currency "GBP" is disabled

    @ui
    Scenario: Not showing the disabled currency
        When I browse that channel
        Then I should not be able to shop using the "GBP" currency

    @ui
    Scenario: Failing to browse channel with disabled default currency
        Given the currency "EUR" is disabled as well
        When I try to browse that channel
        Then I should not be able to shop

    @ui
    Scenario: Browsing a channel with the default currency disabled while using the other one
        Given I am browsing that channel
        And I switch to the "USD" currency
        When the currency "EUR" gets disabled
        Then I should still shop using the "USD" currency
        And I should not be able to shop using the "EUR" currency
