# Lesson first: Auto-Setup and Declarative Configuration

# Prerequisites

Read main [README](../README.md)

# Task Description

In this exercise we will introduce Aggregates support to avoid writing orchestration code.  
For this we will change Order to work as Aggregate, and introduce Repository to store it.   

The code logic will not change along the way, therefore after each exercise tests should still pass:    
```bash
  make tests
```
You can also test placing an order via web interface [localhost:4000](http://localhost:4000).

## 1. Configure Repository for Order Aggregate [Done]

This is already done for convenience.  
Take a look on `src/Infrastructure/Messaging/OrderRepositoryAdapter.php`.

Hints: 
    - [Configure Repository](https://docs.ecotone.tech/modelling/command-handling/repository/configure-repository)

## 2. Introduce Order Aggregate

- Mark `Order` as Aggregate and define identifier
```php
#[Aggregate]
final class Order
{
    #[Identifier]
    private string $orderId;

(...)
```  
- Make `Order::place` a Command Handler
- Record an OrderWasPlaced event within `Order::place`
- Inject `customerId` from metadata into `Order::place`. `PriceCalculator` and `Clock` should be injected automatically.  
- Remove `OrderService::place` method, as it's replaced by Aggregate

After this step tests should still pass.  

Hints:
    - [Aggregate with Factory Method](https://docs.ecotone.tech/modelling/command-handling/state-stored-aggregate/aggregate-command-handlers#aggregate-factory-method)
    - [Publishing Events from Aggregate](https://docs.ecotone.tech/modelling/command-handling/state-stored-aggregate/aggregate-command-handlers#publishing-events-from-aggregate)
    - [Injecting Services and Metadata into Aggregate's method](https://docs.ecotone.tech/modelling/command-handling/state-stored-aggregate/aggregate-command-handlers#calling-aggregate-with-additional-arguments)

## 3. Use Command Routing

- Add routing key `placeOrder` to `Order::placeCommand Handler`
- Change OrderController to send command with routing key `placeOrder`

Hints:
    - [Sending commands with Routing](https://docs.ecotone.tech/modelling/command-handling/external-command-handlers#symfony-laravel-3)

## 4. Avoid transformations in Controllers

- Install Ecotone's JMS support for native deserialization/serialization
```bash
    composer require ecotone/jms-converter
```
- Define Customer Converter `App\Infrastructure\Messaging\UuidConverter`, to tell JMS how to convert UUID to string and back
- Pass JSON to Command Bus and remove transformation from `OrderController::placeOrder`
```php
    $orderId = $this->commandBus->sendWithRouting(
        routingKey: 'placeOrder',
        command: $request->getContent(),
        commandMediaType: 'application/json',
        metadata: ['customer.id' => $customerId],
    );
```

Hints:
    - [Define Converter for Uuid](https://docs.ecotone.tech/modules/jms-converter#custom-conversions-for-classes)
    - [Sending commands with in-flight conversion](https://docs.ecotone.tech/modelling/command-handling/external-command-handlers#sending-commands-with-deserialization)
