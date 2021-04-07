# Introduction

Knowing how to create a web application to manage take-out sales has become essential for a developer in this difficult health period.
The objective of this project is to make an online sales site for motor vehicles. With this site, a user can order one or more items, save specific item baskets, and schedule a time to pick up their items.

# Ubiquity

![banner-duck](https://user-images.githubusercontent.com/75272120/113111752-cc4e2880-9208-11eb-9f80-8c42a4480967.png)
> [Ubiquity](https://ubiquity.kobject.net/), a powerful and fast framework for efficient design.

It combines performance and simplicity of design and use with many interesting features that allows to efficiently program especially a full-stack application like this one… Indeed this framework uses the Model-view-controller architecture or MVC :
* Model part contains the data to display and initializes the variables.
* View part contains the presentation of the graphical interface, the view uses the model.
* Controller part contains logic and processes user actions, modifies model and view data through **routes**.

### Prerequisites

You will need the following things properly installed on your computer.

* php ^8.0.0
* [Git](https://git-scm.com/)
* [Composer](https://getcomposer.org)
* [Ubiquity devtools](https://ubiquity.kobject.net/)

### Installation

* `git clone <repository-url>` this repository
* `cd tds`
* `composer install`

### Running / Development

* `Ubiquity serve`
* Visit your app at [http://127.0.0.1:8090](http://127.0.0.1:8090).


### Further Reading / Useful Links

* [Ubiquity website](https://ubiquity.kobject.net/)
* [Guide](http://micro-framework.readthedocs.io/en/latest/?badge=latest)
* [Doc API](https://api.kobject.net/ubiquity/)
* [Twig documentation](https://twig.symfony.com)
* [Semantic-UI](https://semantic-ui.com)


# This Project

## Datas / Model

#### What it is ?

It concerns all the "profession part". The model represents the real universe in which the application takes place. The data that applies to the business to say it otherwise.

#### Relational data model

![20210308-012646](https://user-images.githubusercontent.com/75272120/113115167-6ebbdb00-920c-11eb-9a29-0cde04a8986d.png)

## Pages / Routes

#### What is it ?

Routes map URLs called by the user to functions in your PHP code. These functions are called "actions". Routes are defined on controller classes.

#### Here is a list of them

Routes | Definition
------------ | -------------
/MyAuth | allows you to connect
/ | displays a dashboard with access to many pages
/order | displays an order history
/store | allows you to navigate through the sections
/section/{idSection} | allows you to view or buy products belonging to a section
/product/{idSection}/{idProduct} | allows you to see the complete sheet of a product
/newBasket | allows you to create a new basket
/basket | allows you to see the content of the baskets already created
/basket/add/{idProduct} | allows you to add a product to the default basket
/basket/addTo/{idBasket}/{idProduct} | allows you to add an item to a specific basket
/basket/timeslot/{idTimeslot} | allows you to choose a timeSlot
/basket/validate | allows you to validate the basket
/basket/command | allows you to validate the order
/basket/clear | allows you to empty the basket


## Controllers


* MyAuth

This controller allows you to control the connection to the application by calling the MyAuthFiles controller present in the _auth_ folder which retrieves the various **views**.

Associated views | Definition
------------ | -------------
disconnected | The logout page
index | The login page
info | The logout and login buttons
message | Display message of confirmation
noAccess | Prevents connection from being made.

* MainController 

Since this is the main controller, there are all the routes on it with all the actions necessary to operate the display and especially the logic of the application. He is also the one who calls for services.

Associated views | Definition
------------ | -------------
basket | displays the contents of the basket by default
index | displays the dashboard
newBasket | displays the cart registration page
orders | displays a list of all commands
product | displays the entire file of a product
section | displays the list of all products in one section
store | displays the list of sections

## Services

Services should have a good deal of logic. Because if we have separate logic, we can theoretically modify our user interface layer or our DAO layer without being affected.
DAO (data access object) is an object to manage the connection to our database. By convention, we name DAO classes with "Repository".

Services | Definition
------------ | -------------
UserRepository | DAO class that provides a technique to separate object persistence and user model data access logic..
