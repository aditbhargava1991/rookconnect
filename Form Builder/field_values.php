<?php
$field_types = [
	'TEXT' => 'Text (Single Line Text)',
	'TEXTBLOCK' => 'Text Block (Multi Line Text)',
	'TEXTBOX' => 'Textbox (Single Line Input)',
	'TEXTBOXREF' => 'Textbox (With Reference Source)',
	'TEXTAREA' => 'Text Area (Multi Line Input)',
	'DATE' => 'Date Box',
	'DATETIME' => 'Date Time Box',
	'TIME' => 'Time Box',
	'SELECT' => 'Dropdown',
	'REFERENCE' => 'Referential Value',
	'RADIO' => 'Radio Buttons',
	'CHECKBOX' => 'Checkbox',
	'CHECKINFO' => 'Checkbox with Detail',
	'SIGNONLY' => 'Signature',
	'SIGN' => 'Signature, Name, and Date',
	'MULTISIGN' => 'Multiple Signatures, Names, and Dates',
	'TABLE' => 'Table',
	'TABLEADV' => 'Table (Advanced)',
	'CONTACTINFO' => 'Contact Information',
	'SLIDER' => 'Slider',
	'SLIDER_TOTAL' => 'Slider Total',
	'SERVICES' => 'Services',
	'ACCORDION' => 'New Form Section (Accordion)'
];
$field_note = [
	'TEXT' => "This field displays a line of text in the Form (no input from the user).",
	'TEXTBLOCK' => "This field displays a block of text in the Form (no input from the user).",
	'TEXTBOX' => "This field is a text input field filled in by the user.",
	'TEXTBOXREF' => "This field is a text input field filled in by the user with advanced Reference Values. This value will be automatically filled in when the Reference Source is chosen (it will fetch the field type chosen in the database). The Reference Source dropdown field is based on fields from this Form that have the Dropdown field type with a Contact Type as the Dropdown Source.",
	'TEXTAREA' => "This field is a text area field filled in by the user.",
	'DATE' => "This field is a date field filled in by the user.",
	'DATETIME' => "This field is a date and time field filled in by the user.",
	'TIME' => "This field is a time field filled in by the user.",
	'SELECT' => "This field is a dropdown field filled in by the user. The values in the Dropdown Source are based on the different Contact types in the software. Choosing the Custom Values value will allow you to input your own dropdown options.",
	'REFERENCE' => "This field is an advanced reference field in which you choose a field from the database and based on the Reference Source, this value will be populated by the chosen value for the Reference Source. The Reference Source dropdown field is based on fields from this Form that have the Dropdown field type with a Contact Type as the Dropdown Source. This is not a user input field and will only display if it is input into the PDF content.",
	'RADIO' => "This field consists of Radio buttons, which are buttons that will only allow the user to check off only one value.",
	'CHECKBOX' => "This field consists of Checkbox buttons, which are buttons that allows the user to check and uncheck as many checkboxes as they would like.",
	'CHECKINFO' => "This field consists of a single Checkbox button with a custom text input value filled in by the user.",
	'SIGNONLY' => "This field is a Signature box.",
	'SIGN' => "This field consists of a Signature box, Name, and Date.",
	'MULTISIGN' => "This field consists of a Signature box, Name, and Date and also allows the user to add multiple Signatures.",
	'TABLE' => "This field generates a table for the user with all the column headings that you add to the table. Ticking the Total checkbox off makes that column numbers only, and will create a sum of the numbers when the PDF is generated.",
	'TABLEADV' => "This field is an advanced table that allows the user to specify the number of rows, columns, where the user puts inputs, table styling, cell styling, row-span, column-span, checkboxes, etc. Should only be used by advanced users.",
	'CONTACTINFO' => "This field will create input fields based on the Contact Type chosen. The available fields to be turned on will be based on the settings for that Contact Type.",
	'SLIDER' => "This field will create a slider with min and max values.",
	'SLIDER_TOTAL' => "This field will create a text value of the total of all selected Sliders.",
	'SERVICES' => "This field will create a list of checkboxes for Services to choose from.",
	'ACCORDION' => "This field creates a new Accordion for the Form as a way to organize the Form better (no input from the user)."
];
?>