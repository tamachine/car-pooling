## API

To simplify the challenge and remove language restrictions, this service must
provide a REST API which will be used to interact with it.

This API must comply with the following contract:

### GET /status

Indicate the service has started up correctly and is ready to accept requests.

Responses:

* **200 OK** When the service is ready to receive requests.

### PUT /cars

Load the list of available cars in the service and remove all previous data
(reset the application state). This method may be called more than once during
the life cycle of the service.

**Body** _required_ The list of cars to load.

**Content Type** `application/json`

Sample:

```json
[
  {
    "id": 1,
    "seats": 4
  },
  {
    "id": 2,
    "seats": 6
  }
]
```

Responses:

* **200 OK** When the list is registered correctly.
* **400 Bad Request** When there is a failure in the request format, expected
  headers, or the payload can't be unmarshalled.

### POST /journey

A group of people requests to perform a journey.

**Body** _required_ The group of people that wants to perform the journey

**Content Type** `application/json`

Sample:

```json
{
  "id": 1,
  "people": 4
}
```

Responses:

* **200 OK** or **202 Accepted** When the group is registered correctly
* **400 Bad Request** When there is a failure in the request format or the
  payload can't be unmarshalled.

### POST /dropoff

A group of people requests to be dropped off. Whether they traveled or not.

**Body** _required_ A form with the group ID, such that `ID=X`

**Content Type** `application/x-www-form-urlencoded`

Responses:

* **200 OK** or **204 No Content** When the group is unregistered correctly.
* **404 Not Found** When the group is not to be found.
* **400 Bad Request** When there is a failure in the request format or the
  payload can't be unmarshalled.

### POST /locate

Given a group ID such that `ID=X`, return the car the group is traveling
with, or no car if they are still waiting to be served.

**Body** _required_ A url encoded form with the group ID such that `ID=X`

**Content Type** `application/x-www-form-urlencoded`

**Accept** `application/json`

Responses:

* **200 OK** With the car as the payload when the group is assigned to a car. See below for the expected car representation 
```json
  {
    "id": 1,
    "seats": 4
  }
```

* **204 No Content** When the group is waiting to be assigned to a car.
* **404 Not Found** When the group is not to be found.
* **400 Bad Request** When there is a failure in the request format or the
  payload can't be unmarshalled.

## Project Overview

This project involves designing and implementing a car pooling service to manage car availability and optimize the use of resources. The system tracks available seats in cars and assigns them to groups of people requesting journeys.

The project is contained in a docker image, create the image and run it.

## Design and Performance Considerations

The system is designed to handle a large volume of requests efficiently. Key considerations include:

- **Car Allocation Algorithm**: Implemented a priority-based algorithm to assign cars to waiting groups while considering fairness and group arrival order.
- **Database Indexing**: Indexes on columns frequently used in queries to improve performance.
- **SQLite Choice**: SQLite was chosen for its simplicity and ease of setup. It is a self-contained, serverless database engine that is ideal for development and testing environments.
- **Concurrency Handling**: Concurrency issues are managed using transactions to ensure data integrity. Additionally, limiting result sets in queries helps in reducing contention and improves performance.
- **Efficiency Design**: The system is designed to be efficient even with large numbers of cars and journeys by optimizing queries, indexing critical columns, and carefully handling database operations to ensure responsiveness and scalability.
- **Queue and Worker Usage**: Implemented a queue with workers to handle asynchronous tasks efficiently. This approach helps in managing tasks such as car assignment and journey processing without blocking the main application flow.
- **Periodic Release**: Tasks are released and processed every 30 seconds to ensure timely updates and resource management. This periodic processing helps in balancing the load and ensures that the system remains responsive.
- **Expiration of Data**: Items are set to expire after 20 minutes to ensure that stale or outdated data does not persist in the system. This mechanism helps in maintaining data accuracy and preventing unnecessary load on the database.
