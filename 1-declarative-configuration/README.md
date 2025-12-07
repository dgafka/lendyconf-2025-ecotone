# Lesson first: Auto-Setup and Declarative Configuration

# Prerequisites

Read main [README](../README.md)

# Task Description

This exercise begins with a standard Symfony application, nothing is preconfigured for you (No Ecotone installed yet).  
This is ecommerce application which allows you to place orders, which as a result send confirmation email and decreases stock.  
The aim of this exercise is to change `placing order flow (OrderController::placeOrder)`, to enable Messaging via Command and Event Handlers.  

At the end of each exercise, your tests should still pass:  

```bash
  make tests
```
You can also test placing an order via web interface [localhost:4000](http://localhost:4000).

## 1. Install Ecotone Framework

- [Install Ecotone's Symfony Bundle](https://docs.ecotone.tech/install-php-service-bus#install-for-symfony)
```bash
  make exec CMD="composer require ecotone/symfony-bundle"
```

> After installation, auto-configuration will kick in automatically.  
> This means all attributes are on your disposal now.   
> Command and Event buses are automatically wired and ready to use.

## 2. Enable Command Handler for placing an order

- Inject `CommandBus` to `OrderController` to send `PlaceOrder` command.
- Add `CommandHandler` to `OrderService` for `place` method.
- Pass customer id as `metadata`

Hints:  
    - [Registering Command Handlers](https://docs.ecotone.tech/modelling/command-handling/external-command-handlers#handling-commands)  
    - [Sending Commands with Metadata](https://docs.ecotone.tech/modelling/command-handling/external-command-handlers#sending-commands-with-metadata)  
    - [Returning Results from Command Handlers](https://docs.ecotone.tech/modelling/command-handling/external-command-handlers#returning-data-from-command-handler)  

## 3. Enable Event Handlers for side effects

- Introduce new Event class `App\Domain\Order\Event\OrderWasPlaced`
- Publish Event using `Event Bus` as a result of placing an Order
- Add separate EventHandlers for Sending Confirmation Email and Decreasing Stock

Hints:  
    - [Registering Event Handlers](https://docs.ecotone.tech/modelling/command-handling/external-command-handlers/event-handling#handling-events)  
    - [Publishing Events](https://docs.ecotone.tech/modelling/command-handling/external-command-handlers/event-handling#publishing-events)  
    - [Multiple Event Handlers](https://docs.ecotone.tech/modelling/command-handling/external-command-handlers/event-handling#multiple-subscriptions)
