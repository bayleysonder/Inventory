# Inventory
Inventory management system that tracks employees, assets, and lists who has which assets currently checked out.








Database Table Structure
Table Assets
int(8) id
varchar(15) assetTag
varchar(13) assetName
int(1) qty
int(1) qtyOut
int(1) categoryid
int(1) outOfOrder
