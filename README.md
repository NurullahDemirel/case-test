1-run "composer install" for vendor

2-set your db info to the env file

3-run "php artisan migrate" for creating tables

4-run "php artisan db:seed" for creating some dummy data;

You can test anymore

System summary: 

I've created booking system. This system have authentication role based(customer-manger-worker e.t.c).

when A customer visits to our system (on web,phone doesen't matter) ı  show  all room and when customer want to make booking have to  filter by date time.

customer filtered the room  and see the available rooms between days these said

customer can make booking between available rooms by giving enter_date and exit_date

ı calculate paid amount (if today is birthday applay %10 discount )

you can find json file to import postman for testing.

System work backend summary.

I write coomand for deleteing exipred booking and updated the capacity of room

Also i should write a event lister for deleteing booking when customer came lobi and pay the  amount and exit the hotel ı have to handle this action and delete booking and update the capacity of room 


