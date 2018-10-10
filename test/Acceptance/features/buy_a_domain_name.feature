Feature:
  Background:
    Given a ".com" domain name costs EUR 10.00
    And the exchange rate EUR to USD is 1.156

  Scenario:
    Given I register "totallyrandomdomainname.com" to "Matthias Noback" with email address "matthiasnoback@gmail.com" and I want to pay in USD
    And I pay 11.56 USD for it
    Then the order was paid
