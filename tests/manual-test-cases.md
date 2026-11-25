# Initial manual test checklist

| ID | Use case | Scenario | Expected result |
|---|---|---|---|
| TC-UC3-01 | UC3 | Place order with active hub and available items | Order and order items are saved; status is SUBMITTED |
| TC-UC3-02 | UC3 | Select inactive hub | Request is rejected |
| TC-UC3-03 | UC3 | Select unavailable menu item | Request is rejected and no order is saved |
| TC-UC8-01 | UC8 | Kitchen marks submitted order ACCEPTED / PREPARING / READY | Order status is updated; READY creates delivery record |
| TC-UC8-02 | UC8 | Invalid kitchen status | API returns validation error |
| TC-UC10-01 | UC10 | Shipper marks order PICKED_UP then DELIVERED | Delivery and order statuses are updated |
| TC-UC10-02 | UC10 | Shipper reports failed delivery | Delivery becomes FAILED and order becomes DELIVERY_FAILED |
| TC-UC13-01 | UC13 | Admin creates hub | Hub is saved with DRAFT status |
| TC-UC13-02 | UC13 | Admin activates hub without active brand | Activation is rejected |
| TC-UC13-03 | UC13 | Admin archives hub with unfinished orders/status not allowed | Archive is rejected |
| TC-UC14-01 | UC14 | Admin creates/updates brand or menu item | Record is saved and returned |
| TC-UC14-02 | UC14 | Delete referenced menu item | Database prevents deletion; record should be archived/deactivated |
| TC-UC16-01 | UC16 | Manager requests reports for date range | Sales, hub workload and delivery performance are returned |
| TC-UC16-02 | UC16 | Non-manager requests report | Deferred: this scaffold has no authentication or role authorization; add it before exposing reports |
