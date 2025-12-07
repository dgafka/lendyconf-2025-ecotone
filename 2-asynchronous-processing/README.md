# Lesson first: Auto-Setup and Declarative Configuration

# Prerequisites

Read main [README](../README.md)

# Task Description

In this exercise we will be changing our side effects (Sending Confirmation Email and Decreasing Stock) to be executed asynchronously.  
For this we will use Database based Message Channel (transport).

This time tests will start to pass, when asynchronous processing will be configured correctly:  
```bash
  make tests
```
You can also test placing an order via web interface [localhost:4000](http://localhost:4000).

## 1. Install Dbal (Database) Module

- Install Dbal module:  
```bash
  make exec CMD="composer require ecotone/dbal"
```

- Configure Dbal module to use default Symfony connection:
```php
// src/Infrastructure/Messaging/DbalConfiguration.php
#[ServiceContext]
public function connectionReference()
{
    return SymfonyConnectionReference::defaultConnection('default');
}
```

Hints:   
    - [Install Dbal Module for Symfony](https://docs.ecotone.tech/modules/symfony/symfony-database-connection-dbal-module#using-existing-connections-recommended)  
    - [Extension configuration objects](https://docs.ecotone.tech/messaging/service-application-configuration#extension-objects)  

> This configuration auto-configures following features:  
> - Transactional message handling - Ensures data consistency by enabling transactions for message processing.  
> - Automatic message deduplication - Prevents the same message from being processed twice, protecting us from duplicate orders, double payments, etc.  
> - Database message channels - Allows to use reliable and durable queuing by storing messages in the database.  


## 2. Enable Asynchronous Processing

- Introduce `Dbal Message Channel` (Database) with name `async`
- Make Event Handlers asynchronous by using `async` channel
- Run consumer and place the order 

Hints:  
    - [Dbal Message Channel](https://docs.ecotone.tech/modules/dbal-support#message-channel)  
    - [Service configuration](https://docs.ecotone.tech/messaging/service-application-configuration#extension-objects)  
    - [Asynchronous Processing](https://docs.ecotone.tech/modelling/asynchronous-processing)  
