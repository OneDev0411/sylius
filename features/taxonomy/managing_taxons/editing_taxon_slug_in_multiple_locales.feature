@managing_taxons
Feature: Editing taxon's slug in multiple locales
    In order to manage access path to taxon page in many languages
    As an Administrator
    I want to be able to edit taxon's slug in multiple locales

    Background:
        Given the store is available in "English (United States)"
        And the store is also available in "Polish (Poland)"
        And I am logged in as an administrator

    @ui @javascript
    Scenario: Creating a root taxon with an autogenerated slug
        Given I want to create a new taxon
        When I specify its code as "MEDIEVAL_WEAPONS"
        And I name it "Medieval weapons" in "English (United States)"
        And I name it "Bronie średniowieczne" in "Polish (Poland)"
        And I add it
        Then this taxon should have slug "medieval-weapons" in "English (United States)"
        And this taxon should have slug "bronie-sredniowieczne" in "Polish (Poland)"

    @ui @javascript
    Scenario: Creating a child taxon with an autogenerated slug
        Given the store has taxonomy named "Medieval weapons" in "English (United States)" locale and "Bronie średniowieczne" in "Polish (Poland)" locale
        And I want to create a new taxon for "Medieval weapons"
        When I specify its code as "SIEGE_ENGINES"
        And I name it "Siege engines" in "English (United States)"
        And I name it "Machiny oblężnicze" in "Polish (Poland)"
        And I add it
        Then this taxon should have slug "medieval-weapons/siege-engines" in "English (United States)"
        And this taxon should have slug "bronie-sredniowieczne/machiny-obleznicze" in "Polish (Poland)"

    @ui
    Scenario: Creating a root taxon with a custom slug
        Given I want to create a new taxon
        When I specify its code as "MEDIEVAL_WEAPONS"
        And I name it "Medieval weapons" in "English (United States)"
        And I set its slug to "mw" in "English (United States)"
        And I name it "Bronie średniowieczne" in "Polish (Poland)"
        And I set its slug to "bs" in "Polish (Poland)"
        And I add it
        Then this taxon should have slug "mw" in "English (United States)"
        And this taxon should have slug "bs" in "Polish (Poland)"

    @ui
    Scenario: Seeing disabled slug field when editing a taxon
        Given the store has taxonomy named "Medieval weapons" in "English (United States)" locale and "Bronie średniowieczne" in "Polish (Poland)" locale
        When I want to modify the "Medieval weapons" taxon
        Then the slug field should not be editable in "English (United States)"
        Then the slug field should also not be editable in "Polish (Poland)"

    @ui @javascript
    Scenario: Prevent from editing a slug while changing a taxon name
        Given the store has taxonomy named "Medieval weapons" in "English (United States)" locale and "Bronie średniowieczne" in "Polish (Poland)" locale
        When I want to modify the "Medieval weapons" taxon
        And I rename it to "Renaissance weapons" in "English (United States)"
        And I rename it to "Bronie renesansowe" in "Polish (Poland)"
        And I save my changes
        Then this taxon should have slug "medieval-weapons" in "English (United States)"
        Then this taxon should have slug "bronie-sredniowieczne" in "Polish (Poland)"

    @ui @javascript
    Scenario: Automatically changing a taxon's slug while editing a taxon's name
        Given the store has taxonomy named "Medieval weapons" in "English (United States)" locale and "Bronie średniowieczne" in "Polish (Poland)" locale
        When I want to modify the "Medieval weapons" taxon
        And I enable slug modification in "English (United States)"
        And I rename it to "Renaissance weapons" in "English (United States)"
        And I enable slug modification in "Polish (Poland)"
        And I rename it to "Bronie renesansowe" in "Polish (Poland)"
        And I save my changes
        Then this taxon should have slug "renaissance-weapons" in "English (United States)"
        Then this taxon should have slug "bronie-renesansowe" in "Polish (Poland)"

    @ui @javascript
    Scenario: Manually changing a taxon's slug while editing a taxon's name
        Given the store has taxonomy named "Medieval weapons" in "English (United States)" locale and "Bronie średniowieczne" in "Polish (Poland)" locale
        When I want to modify the "Medieval weapons" taxon
        And I enable slug modification in "English (United States)"
        And I rename it to "Renaissance weapons" in "English (United States)"
        And I set its slug to "renaissance" in "English (United States)"
        And I enable slug modification in "Polish (Poland)"
        And I rename it to "Bronie renesansowe" in "Polish (Poland)"
        And I set its slug to "renesansowe" in "Polish (Poland)"
        And I save my changes
        Then this taxon should have slug "renaissance" in "English (United States)"
        Then this taxon should have slug "renesansowe" in "Polish (Poland)"
