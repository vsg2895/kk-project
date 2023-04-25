# Körkortsjakten

Short movie about the project
https://vimeo.com/318239541/8d48c73053

## Tools

### Slack
korkortsjaktengroup.slack.com

### Project management tool
https://trello.com/b/9HsPyUmC/dev


## Environments 

### Production
https://www.korkortsjakten.se

### Staging 
https://staging.korkortsjakten.se/  

#### Test Accounts

The password for all test accounts is _jakten_

* admin1@jakten.dev
* student1@jakten.dev
* employee1@jakten.dev

#### Email testing

Emails sent from staging are trapped in Mailtrap
https://mailtrap.io
kontakt@korkortsjakten.se
JF2SNhNf2XAU5mdg

## Setup

* Clone the repository
* Create a new database
* Copy `.env.example` to `.env` and adjust to your local setup
* install php dependencies `composer install`
* `php artisan key:generate`
* `php artisan migrate`
* `php artisan db:seed`
* install javascript dependencies `npm install`
* run webpack `npm run dev`


## Deployment

To production:
 * https://envoyer.io/auth/login
 * Choose Korkortsjakten project and press `Deploy` button



## Statistics

### API

The Statistics API consist of several URLs that can be accessed via a GET request. All additional values are optional and default values are used when needed. All data is filtered by the organization for the current user, admin user is not filtered in any way. 

#### Options
##### Granularity
Granularity determines the level of consolidation of the data the available levels are listed below. If the option is required it will default back to monthly on value error.  
* `hourly`
* `daily`
* `weekly`
* `monthly`
* `yearly`
 
##### Type
Type determines the second level consolidation or acts a select if a id value is supplied. 
The available levels are listed below.
* `city`
* `school`
* `organization`

#### ConnectedIn
Determines the count type. 
* 1, is connected
* 2, is not connected
* 3 or no value, all.

#### Endpoints

* /api/statistics/orders/{granularity?}/{type?}/{id?}
  * Returns a count and sum of orders 
    * Example request: GET /api/statistics/orders/
    * Example results: `[{"2014-10":{"orders":100,"amount":"45678.00"}}]`
* /api/statistics/courses/{granularity?}/{type?}/{id?}
  * Returns a sum of courses (Passed and Future)
    * Example request: GET /api/statistics/courses/yearly/organization
    * Example results: `{"6":{"2014":{"sum":1},"2015":{"sum":12},"2016":{"sum":18},"2017":{"sum":6}}}`
* /api/statistics/cancellations/{granularity?}/{type?}/{id?}
  * Returns a sum and count of orders canceled (Passed and Future)
    * Example request: GET /api/statistics/cancellations/
    * Example results: `[{"2017-03":{"orders":4,"amount":"2500.00"}}]`
* /api/statistics/contact/{granularity?}/{type?}/{id?}
  * Returns a sum of contact mediated (Passed and Future)
    * Example request: GET /api/statistics/contact/
    * Example results: `{"2017-04":[7],"2017-05":[28]}`
* /api/statistics/schools/{connectedIn?}
  * Returns a sum of schools by connected status
    * All
        * Example request: GET /api/statistics/schools/
        * Example results: `{"num":1000}`
    * Connected
        * Example request: GET /api/statistics/schools/1
        * Example results: `{"num":200}`
* /users
  * Returns a sum of by user role. Only available to admin users.
      * Example request: GET /api/statistics/users
      * Example results: `{"1":{"sum":1000},"2":{"sum":210},"3":{"sum":5}}`
  
### ENV

#### StatisticsServiceCache
If data should be Cache at all.
Yes or No value, default No.

#### StatisticsServiceCacheAll
Determines if all organisations data should be Cache when cron service is run.
Yes or No value, default No.

### SVGs

run `npm run svgo` when adding new svgs to project. 

