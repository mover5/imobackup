
function setDay(month) {
var daybox = document.form.day;

daybox.options.length = 0;
var i=1;
if (month == "1" || month == "3" || month == "5" || month == "7" || month == "8" || month == "10" || month == "12") {
	for (i=1;i<=31;i++)
	{
		daybox.options[daybox.options.length] = new Option(i,i);
	}
}
if (month == "4" || month == "6" || month == "9" || month == "11") {
	for (i=1;i<=30;i++)
	{
		daybox.options[daybox.options.length] = new Option(i,i);
	}
}
if (month == "2") {
	for (i=1;i<=29;i++)
	{
		daybox.options[daybox.options.length] = new Option(i,i);
	}
}

}
