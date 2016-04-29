@managing_coupons
Feature: Browsing promotion coupons
    In order to see all promotion coupons
    As an Administrator
    I want to browse coupons

    Background:
        Given the store operates on a single channel in "France"
        And there is a promotion "Christmas sale"
        And it has 1 coupon with code "XYZ"
        And I am logged in as an administrator

    @ui
    Scenario: Browsing coupons in store
        Given I want to see all related coupons to this promotion
        Then I should see 1 coupon on the list
        And I should see the coupon with code "XYZ"
