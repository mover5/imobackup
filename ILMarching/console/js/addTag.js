function addTags(myField, start, end){
	//IE support
	if (document.selection) {
		myField.focus();
		sel = document.selection.createRange();
		sel.text = start+sel.text+end;
	}
	//MOZILLA/NETSCAPE support
	else if (myField.selectionStart || myField.selectionStart == '0') {
		var startPos = myField.selectionStart;
		var endPos = myField.selectionEnd;
		myField.value = myField.value.substring(0, startPos)
		+ start
		+ myField.value.substring(startPos, endPos)
		+ end
		+ myField.value.substring(endPos, myField.value.length);
	} else {
		myField.value += start + end;
	}
}