# System tests

We currently have a bit of a problem with the system tests. The scenario which tests buying a domain name and paying in a different currency will fail *every day*, because the exchange rate will be different *every day*. This is not a good situation to be in.

In order to make the tests not fail (seemingly) randomly, we need to do two things:

1. Take a look at the documentation of the [Swap](https://github.com/florianv/swap) library that we've used in this project. There is an option to retrieve the exchange rate for a fixed date. We can use this, but first we'd have to lock the server time somehow. To do this, we can introduce some sort of a `Clock` object, which we can ask for the current time. Next, we influence the web server to set a fixed time on this object, based on an environment variable (see `docker-compose.test.yml` for some inspiration).
2. Next, we can apply the *Dependency inversion principle* and introduce an interface for an object that calculates the exchange rate. This brings us in the position of replacing the real implementation with a *fake* implementation; one that we control. Once you've come up with a alternative implementation for the exchange rate service, make sure it is used instead of the original one. You can do so in `app/container.php`, and use the `$applicationEnv` to make the decision.

We still have the same issue with the `whois` service. Some day our tests may all fail, because somebody registers `totallyrandomdomainname.com`. But at least we've fixed the biggest issue.

> This technique of introducing fake replacement services can be used to make system tests more likely to succeed. But it can also be used to make system tests safer to execute. Services you'll often replace with fake implementations are mail servers, payment providers, etc. 

# Acceptance tests

Our application has at least one meaningful use case. You could explain to a non-programmer what the domain registration process looks like. But you can't verify that you've implemented the use case correctly, without invoking all the slow infrastructure that this project has. To improve that situation we can take the following steps.

> Please run all the tests after every change to make sure you don't end up with an unfixable application.

1. Rewrite the use cases to make them independent of the web framework (i.e. Zend Expressive). Move the code that doesn't deal with `Request` objects, routing, or template rendering to dedicated services (so-called "application services"). You may still call the real external services. We'll replace those later. 
2. Write a scenario that describes in non-web-framework-language what's going on. E.g.

    Given I want to check the availability of totallyrandomdomainname.com
    And it turns out to be available
    When I register it
    And I fill in my name ("Matthias Noback") and email address ("matthiasnoback@gmail.com")
    And I pay EUR 10.00 for it
    Then the domain name is mine

3. Create step definitions for all the steps in `Test\Acceptance\FeatureContext`. Instantiate the application services and their dependencies manually.
4. Note that we're still depending on a lot of infrastructure (database, whois, exchange rates) and we're mainly spending time testing that code. Introduce abstractions for all of them, and provide fake objects. In order to be useful, they need methods which you can call to manually set data. Do this as part of the scenario itself, e.g.:

    Given the exchange rate of EUR to USD is "1.15878"
    
    Given the domain name totallyrandomdomainname.com is still available

Meanwhile, take note of how much faster the acceptance tests are becoming.

> Note that even if you use Mink, you can still write the scenarios in plain English, without referring to form fields, URLs, specific HTML elements or even CSS classes.

# Integration tests

TODO

# Unit tests

TODO
