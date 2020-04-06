// Get the modal
var modal = document.getElementById('myModal');

$(function(){
	var currentID = 0;
	
	// Get the image and insert it inside the modal - use its "alt" text as a caption
	var img = document.getElementById('myImg');
	var modalImg = document.getElementById("img01");
	var captionText = document.getElementById("caption");
	
	$(".myImg").on("click", function(){
		modal.style.display = "block";
		modalImg.src = $(this).attr("src");
		captionText.innerHTML = $(this).attr("alt");
		currentID = $(this).data("id");
	});
	
	$(".close").on("click", function(){
		modal.style.display = "none";
	});
	
	$(".left").on("click", function(){
		var id = parseInt(currentID) - 1;
		id = id <= 0 ? $("#maxImg").val() : id;
		console.log(id);
		modalImg.src = $("#Img-" + id).attr("src");
		captionText.innerHTML = $("#Img-" + id).attr("alt");
		currentID = id;
	});	
	$(".right").on("click", function(){
		var id = parseInt(currentID) + 1;
		id = id > $("#maxImg").val() ? 1 : id;
		console.log(id);
		modalImg.src = $("#Img-" + id).attr("src");
		captionText.innerHTML = $("#Img-" + id).attr("alt");
		currentID = id;
	});
});