# path-case

Install
----
    
    $ git@github.com:emregunevi/path-case.git
    $ cd /Projenin konumu
    $ composer install
    $ php bin/console doctrine:migrations:migrate
    
Start
---
Uygulamayı ayağa kaldırmak için:

    $ php bin/console server:run localhost:8080
Api Kısmı: 
---
Login işlemi için:
       
       POST http://localhost:8080/login
           {
              "userName": "emregunevi",
              "password": "Emre123*"
           }
       ile login olabilirsiniz.

Add işlemi için:
    
        POST http://localhost:8080/order/add
            {
                "productId": 1,
                "quantity": 10,
                "address": "Deneme",
                "shippingDate": "2020-12-31"
            }

Edit işlemi için:
    
        POST http://localhost:8080/order/edit/{orderId}
        
            {
                "productId": 1,
                "quantity": 8,
                "address": "Deneme",
                "shippingDate": "2020-12-31"
            }

Get işlemi için:
    
        GET http://localhost:8080/order/get/{orderId}
    
Get all işlemi için
    
        GET http://localhost:8080/order/get-all