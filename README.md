# Inventory
Languages: html, css, javascript, php, and MySQL
Frameworks: Bootstrap 5, chart.js


This is a full stack web application that helps an organization track their assets. This was ran on a WAMP server using myphpadmin, MySQL, MariaDB.

This helped the IT staff to keep track of the amount of IT assets we have in the organization and would also keep track of who had what checked out in the organization.
The application uses frontend languages to allow an elevated user to make changes to a relational MySQL database. This would allow for future queries or any asset tracking that the department might need. Using these queries, I made a dashboard that had real time updates depending on the query generated, this was used to at a glance see how many laptops we had available and how many that were currently checked out.

--Application logic--

Employees can access inventory tracking system by creating an account on the website.

It will do a cross check to see if this user already has a employee account, if so it will update the employee table with the userid that they just created. These accounts are now paired. (This allows for employees to be entered into the database before they personally create their account.) 

if the user does not have elevated access, they will only be able to link their account and to look at the inventory and assets checked out. 

For elevated users this is a front-end web application that can make changes to myphpadmin database. This is used for the IT department to help keep track of Laptops, desktops, All-in-one systems, Monitors, docks, and iPads.
