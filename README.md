1-run "composer install" fro vendor


2-set your db info 

3-run "php artisan migrate"

4-run "php artisan db:seed" for creating some dummy data;

You can test anymore

System summary: 

I've created booking system. This system have authentication role based(customer-manger-worker e.t.c).

when A customer visits to our system (on web,phone doesen't matter) ı  show  all room and when customer want to make booking have to  filter by date time.

customer filtered the room  and see the available rooms between days these said 

customer can make booking between available rooms by giving enter_date and exit_date

ı calculate paid amount (if today is birthday applay %10 discount )


