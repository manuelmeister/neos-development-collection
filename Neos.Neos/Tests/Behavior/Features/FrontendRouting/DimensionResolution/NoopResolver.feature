@contentrepository
Feature: NoopResolver does nothing (boilerplate testcase)

  Background:
    Given I use no content dimensions
    And the following NodeTypes to define content repository "default":
    """
    'Neos.ContentRepository:Root': []
    """

  Scenario: Match homepage URL
    When I am on URL "/"
    And I invoke the Dimension Resolver from site configuration:
    """
    contentRepository: default
    contentDimensions:
      resolver:
        factoryClassName: Neos\Neos\FrontendRouting\DimensionResolution\Resolver\NoopResolverFactory
    """
    Then the resolved dimension should be '{}' and the remaining URI Path should be "/"

  Scenario: Match homepage URL
    When I am on URL "/foo"
    And I invoke the Dimension Resolver from site configuration:
    """
    contentRepository: default
    contentDimensions:
      resolver:
        factoryClassName: Neos\Neos\FrontendRouting\DimensionResolution\Resolver\NoopResolverFactory
    """
    Then the resolved dimension should be '{}' and the remaining URI Path should be "/foo"
