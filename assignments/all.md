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

1. Rewrite the registration use case to make it independent of the web framework (i.e. Zend Expressive). Move the code that doesn't deal with `Request` objects, routing, or template rendering to a dedicated service (a so-called "application service"). You may still call the real exchange rate service and database. We'll replace those later. 
2. Take the following scenario that describes in non-web-framework-language what's going on and save it as `test/Acceptance/features/buy_a_domain_name.feature`:

    Feature:
      Scenario:
        Given I register "totallyrandomdomainname.com" to "Matthias Noback" with email address "matthiasnoback@gmail.com" and I want to pay in USD
        And I pay 11.56 USD for it
        Then the order was paid

3. Create step definitions for all the steps in `Test\Acceptance\FeatureContext`. Instantiate the application services and their dependencies manually.
4. Note that we're still depending on infrastructure code (related to the database and the exchange rate service) and we're mainly spending time testing that code. Introduce abstractions for each of these, and add fake implementations. In order to be useful, they need methods which you can call to manually set data. Do this as part of the scenario itself, e.g.:

    Feature:
      Background:
        Given a .com domain name costs EUR 10.00
        And the exchange rate EUR to USD is 1.156

Meanwhile, take note of how much faster the acceptance tests are becoming.

> Note that even if you use Mink, you can still write the scenarios in plain English, without referring to form fields, URLs, specific HTML elements or even CSS classes.

# Integration tests

We now have:

1. System tests with the "dangerous" dependencies switched out. 
2. An acceptance test with a specification written in plain English, and with all the port adapters replaced by something faster and simpler ("fakes"). 

For each of these adapters we defined an interface; a contract for communication. Now we need to verify that our assumptions about the implementation are indeed correct. So we should write *integration tests* for all the implementations we created (e.g. the exchange rate service and the repositories). We prove that all this code functions correctly. While testing this code, we don't use any test doubles; we test *the real thing*. We make a real call over the network to the Fixer exchange rate service and we store real files.

> Note that any errors in our assumptions, any mistake we made in interpreting the external service's API documentation, any failure on their side (a failed release, a backwards compatibility break, etc.), will show up while running the integration tests. This is a huge improvement on the current situation, because without integration tests, the problem would jump in our face when running the system tests; and in that case, who could tell us immediately what's wrong?  

# Unit tests

Having tested the main use case of this application, we should take a look at the design of its smaller units. We didn't write unit tests while developing this code, but we are going to add them now. This should help us when we're going to improve the domain model later on. We want to prove that on their own, these domain objects encapsulate their data well (i.e. they protect their domain invariants) and have intention-revealing interfaces.

Find ways to restructure the code, and migrate the model from an anemic one, with only getters and setters and almost no encapsulation, to a rich one, with a predefined set of behavior. For example: an `Order` can only be created in one way. It can only be modified in one way. Try to make these different usages clear in the code and test each of them.

Find ways to encapsulate domain concepts like "email address", "currency", "amount", "domain name", etc. Make sure that every domain object can only exist in a complete, consistent, and valid state.

While you're at it, you can clean up the code in the `RegisterController` and extract a class which simply calculates the price, using the exchange service, and the pricing repository.

# Working effectively with tests

In order to not be held back by the test suite, you need to be able to:

- Run and rerun single tests, groups of tests, types of tests.
- Easily find out why a test fails.

In order to do this, you could use some or all of the following:

- PHPUnit suites, groups & filters
- Behat suites and tags
- Step debugging (using XDebug)
- Maximal verbosity of command line output
- Special error renderers that don't spit out a lot of HTML (when running your system tests)
- Exception and error logging (built-in; check out `docker-compose logs -f web`)
- A way to open the last failed response in a browser (see also MinkContext's `@Then show last response` step definition)
- Last *and* least: dumping variables using Symfony's `dump()` function.

Give these things a try, and make sure you're not slowed down too much while working on your test suite! 
