# Ecotone - Lendable 2025 - Boilerplate-free Messaging Workshop

In this repository you will find all materials for the Ecotone workshop.

# Context

In this workshop, we'll learn Ecotone's fundamentals—**how to use declarative configuration and a auto-configuration approach to build message-driven systems.**     
We will be working with a real e-commerce application where customers can browse products, add them to their cart, and complete purchases.          
This is a standard Symfony application without any messaging capabilities yet. We'll install and configure Ecotone ourselves as part of the learning process, to see how simple it is to add powerful messaging capabilities to an existing application.       
  
By the end, we'll see how **Ecotone's declarative style eliminates configuration files and reduces development time to minimum.**  

----

The workshop is divided into three sections (exercises). Each is built on top of previous one.

1. [1-declarative-configuration](./1-declarative-configuration) - Auto-Setup and Declarative Configuration
2. [2-asynchronous-processing](./2-asynchronous-processing) - Asynchronous Processing
3. [3-modelling](./3-modelling) - Modelling with Messaging
4. [4-final-solution](./4-final-solution) - Final Solution

# Requirements

- OS required: Linux, MacOS.  
- Execution platform: [Docker](https://docs.docker.com/engine/install/) and [Docker-Compose](https://docs.docker.com/compose/install/).
- IDE: PHPStorm or free [Visual Studio Code](https://code.visualstudio.com/) to edit code with the [PHP](https://marketplace.visualstudio.com/items?itemName=DEVSENSE.phptools-vscode) plugin.

# Run specific exercise

Enter specific exercise directory and run the following command:

#### Run make up to start the application.
```bash
  make up
```

#### You should be able then to open the Application under:
```bash
  localhost:4000
```

#### If application doesn't start, you can check the logs with:
```bash
  make logs
```

#### To run tests within the container, run:

```bash
  make test
```

#### To shell into the container, run:
```bash
  make shell
```

#### Before proceeding with next exercises, do the clean up with:
```bash
  make down
```

# Hints

1. To avoid reaching composer install limits during the workshop (same ip, multiple requests), some dependencies are already installed and commited to the repository.