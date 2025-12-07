# Ecotone - Lendable 2025 - Boilerplate-free Messaging Workshop

In this repository you will find all materials for the Ecotone workshop.

# Context

In this Workshop we will learn Ecotone's fundamentals, on how to use declarative configuration and config-lite approach to build Message-Driven Systems.
This workshop is built on top of e-commerce application, which is application that allows users to buy products.  
This is standard Symfony application with Ecotone's Symfony Bundle installed, nothing extra is added.


The workshop is divided into three sections (exercises). Each is built on top of previous one.

1. [1-declarative-configuration](./1-declarative-configuration) - Auto-Setup and Declarative Configuration
2. [2-asynchronous-processing](./2-asynchronous-processing) - Asynchronous Processing
3. [3-modelling](./3-modelling) - Modelling with Messaging
4. [4-final-solution](./4-final-solution) - Final Solution

# Requirements

OS required Linux, MacOS.
To run the workshop you only need [Docker](https://docs.docker.com/engine/install/) and [Docker-Compose](https://docs.docker.com/compose/install/).

If you don't have PHPStorm, you can use the free [Visual Studio Code](https://code.visualstudio.com/) to edit code with the [PHP](https://marketplace.visualstudio.com/items?itemName=DEVSENSE.phptools-vscode) plugin.

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

#### Before proceeding with next exercises, do the clean up with:
```bash
  make down
```

# Hints

1. Do avoid reaching composer install limits during the workshop (same ip, multiple requests), some dependencies are already installed and commited to the repository.