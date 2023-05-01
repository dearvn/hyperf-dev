/**
 *
 * @param {*} src file url link
 * @param {*} fileName file name + suffix
 * @param {*} fileType file type (suffix)
 * @param {*} isNotImage whether it is an image
 */
export function download(src, fileName, fileType, isNotImage) {
  if (isNotImage) {
    //Determine whether it is a picture in chrome
    fileLinkToStreamDownload(src, fileName, fileType);
  } else {
    ImgtodataURL(src, fileName, fileType);
  }
}

function fileLinkToStreamDownload(url, fileName, type) {
  let xhr = new XMLHttpRequest();
  xhr.open("get", url, true);
  xhr.setRequestHeader("Content-type", `application/${type}`);
  xhr.responseType = "blob";
  xhr.onload = function() {
    if (this.status == 200) {
      var blob = this.response;
      downloadNormalFile(blob, fileName);
    }
  };
  xhr.send();
}

function downloadNormalFile(blob, filename) {
  var eleLink = document.createElement("a");
  let href = blob;
  if (typeof blob == "string") {
    eleLink.target = "_blank";
  } else {
    href = window.URL.createObjectURL(blob); //Create a download link
  }
  eleLink.href = href;
  eleLink.download = filename; //file name after download
  eleLink.style.display = "none";
  // trigger click
  document.body.appendChild(eleLink);
  eleLink.click(); //click to download
  //The download is complete and the element is removed
  document.body.removeChild(eleLink);
  if (typeof blob == "string") {
    window.URL.revokeObjectURL(href); //释放掉blob对象
  }
}

function ImgtodataURL(url, filename, fileType) {
  getBase64(url, fileType, _baseUrl => {
    // Create hidden downloadable links
    var eleLink = document.createElement("a");
    eleLink.download = filename;
    eleLink.style.display = "none";
    // Image to base64 address
    eleLink.href = _baseUrl;
    // trigger click
    document.body.appendChild(eleLink);
    eleLink.click();
    // then remove
    document.body.removeChild(eleLink);
  });
}

/**
 * The img instance created through the constructor will download the image immediately after giving the src value
 * @param {*} url
 * @param {*} fileType
 * @param {*} callback
 */
function getBase64(url, fileType, callback) {
  var Img = new Image(),
    dataURL = "";
  Img.src = url;
  Img.setAttribute("crossOrigin", "anonymous");
  Img.onload = function() {
    //To ensure that the picture is fully obtained, this is an asynchronous event
    var canvas = document.createElement("canvas"), //Create the canvas element
      width = Img.width, //Make sure the size of the canvas is the same as the picture
      height = Img.height;
    canvas.width = width;
    canvas.height = height;
    canvas.getContext("2d").drawImage(Img, 0, 0, width, height); //Draw the picture into the canvas
    dataURL = canvas.toDataURL("image/" + fileType); //Convert image to data url
    callback ? callback(dataURL) : null;
  };
}

/**
 * Instructions
 * fileByBase64(file, (base64) => {
      console.log(base64)
   })
 * Upload attachment to base64
 * @param {File} file file stream
 */
export const fileByBase64 = (file, callback) => {
  var reader = new FileReader();
  // Pass in a parameter object to get the text content based on the parameter object
  reader.readAsDataURL(file);
  reader.onload = function(e) {
    // target.result This attribute represents the DataURL of the target object
    callback(e.target.result);
  };
};

/**
 * Convert bytes to appropriate units
 * @param {*} size
 */
export function getfilesize(size) {
  //Convert bytes to normal file size
  if (!size) return "";
  var num = 1024.0; //byte
  if (size < num) return size + "B";
  if (size < Math.pow(num, 2)) return (size / num).toFixed(2) + "KB"; //kb
  if (size < Math.pow(num, 3))
    return (size / Math.pow(num, 2)).toFixed(2) + "MB"; //M
  if (size < Math.pow(num, 4))
    return (size / Math.pow(num, 3)).toFixed(2) + "G"; //G
  return (size / Math.pow(num, 4)).toFixed(2) + "T"; //T
}
