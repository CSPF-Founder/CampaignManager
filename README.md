# Campaign Manager (CM)

Campaign Manager (CM) provides Single dash-board for Political leadership-in the run-up to the elections. 

* You can use this open source application and set it up in your own server(setup instructions are below).
* AVS Labs Pte Ltd(http://avslabs.co/) also offers the Campaign Manager SAAS in a nominal price.
* Customization: You can also contact AVS Labs, if you need customized version ( it can be done in a nominal price).

#### Key Benefits for political party leadership –
* Delegates different tasks to party hierarchy with delivery deadlines
* Tracks progress and provides auto alerts to leader on tasks’ status
* Prioritizes Constituency Tasks in a criticality matrix, for better focus
* Provides Simple Dashboard to manage election campaign, by any parameter – Issue/Delegate/Constituency/Criticality 
	
	
## Setup

### Setup Requirements
* OS: Debian 9 or higher version (or can be installed in other Linux variants)
* Server: Apache (latest version)
* Server-Side Scripting Engine: PHP  7.x
* Database: Mariadb 10.3 or higher version

#### Step 1:
Download the web source code and place in a directory, for example: /opt/cm/web/
Edit the web/App/Config.php file and modify the Database configuration details
(create the database called "cm" first : mysql> create database cm;).

#### Step 2:
Configure the apache with following or similar configuration so that it will mark "/opt/cm/web/public" as root of the publicly exposed directory(don't expose the root directory to public).

        DocumentRoot /opt/cm/web/public
        
        <Directory /opt/cm/web/>
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
        </Directory>
        
#### Step 3:
Create a folder called "data" above the web folder(exmaple: /opt/cm/data/" and ensure the apache server has full permission over this directory (ensure this directory is NOT publicly accessible). 

#### Step 4:
Edit the "web/App/Config.php" file and set the debug mode to true
Once everything is ready, go to "http(s)://[SERVER_ADDRESS]/setup/"
Enter the Super Admin username and password then click Setup.

#### Step 5:
If everything goes well, you should be able to see success message.  Then disable the debug mode by setting DEBUG_MODE value to false in "web/App/Config.php" file.

#### Step 6:
Go to "http(s)://[SERVER_ADDRESS]/" and login with super admin.

#### Step 7:
Create organization and add an admin user to the organization.  Once done, log out from the super admin.

#### Step 8:
Log in into the "http(s)://[SERVER_ADDRESS]/" with the newely created admin credentials.


## Usage

### Constituency Addition
  #### Step1: 
  Login with the above admin credentials
  #### Step 2:
  Select “Constituency->Add” Menu
  #### Step 3:
  Fill the constituency name and party strength(strong,moderate/weak) in the constituency
	
### Users

#### Feeder account:

    • Feeder account is used by party workers at party level. 
    • This account can be used for adding new feed and view their tasks.
    • If there is a task, the feeder can mark as completed after finishing the work or give feedback to the admin.
	
#### Creating a Feeder Account:	
		(at least one constituency should be there in order to add feeder account)
	
		Step 1:
			Select “Users->Add” Menu

		Step 2:
			Fill the form , select the role as Feeder

		Step 3:
			Click “Add” button

#### Admin account:
	
    • The admin user can view the feeds given by feeders. 
    • Admin can create new task or task based on the feed
    • Admin dashboard allows to view the overdue tasks, completed tasks that are yet to be verified by the admin, other tasks that are still not resolved.
    • Admin can create constituencies 
    • Admin can create feeder, data entry(subadmin) accounts

	
	Creating admin Account:	
	
		Step 1:
			Select “Users->Add” Menu

		Step 2:
			Fill the form , select the role as Admin

		Step 3:
			Click “Add” button

#### Data Entries/subadmin account:

    • The data entry account can add a feed on behalf of the feeders.

	Creating the Account:	
	
		Step 1:
			Select “Users->Add” Menu

		Step 2:
			Fill the form , select the role as Subadmin

		Step 3:
			Click “Add” button

 #### Mobile Client Repository:
 https://github.com/CSPF-Founder/CampaignManagerMobile
