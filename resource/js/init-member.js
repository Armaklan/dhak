
function initMember(user) {

	id = $(user).children(".id").val();
	long_name = $(user).children(".long_name").val();
	mail = $(user).children(".mail").val();
	tel = $(user).children(".tel").val();
	adresse = $(user).children(".adresse").val();
	city = $(user).children(".city").val();
	cp = $(user).children(".cp").val();
	profil = $(user).children(".profil").val();
	formation = $(user).children(".formation").val();
	
	popup = $('#updateUserModal');
	popup.find("input[name$=inputName]").val(long_name);
	popup.find("input[name$=inputId]").val(id);
	popup.find("input[name$=inputEmail]").val(mail);
	popup.find("input[name$=inputTel]").val(tel);
	popup.find("input[name$=inputAdresse]").val(adresse);
	popup.find("input[name$=inputCity]").val(city);
	popup.find("input[name$=inputCP]").val(cp);
	popup.find("input[name$=inputProfil]").prop('checked', false);
	popup.find("input[value='" + profil + "']").prop('checked', true);

	popup.find("select[name$=inputFormation] option").prop('selected', false);
	popup.find("select[name$=inputFormation]").find("option[value='" + formation + "']").prop('selected', true);

	popup.modal('show');

}
