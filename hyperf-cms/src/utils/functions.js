import { confirm } from "element-ui";

/**
 * Multidimensional array specifies sub-item flattening function
 * @param array the flattened array to perform
 * @param childrenKeys to participate in the flat array of child key names default ['children']
 * @param flattenParent default parent array
 * @param flattenParentKey After being flattened, the child's parent array stores the key name
 * @returns {Array}
 */
export function arrayChildrenFlatten(
  array,
  { childrenKeys, flattenParent, flattenParentKey } = {}
) {
  childrenKeys = childrenKeys || ["children"];
  flattenParent = flattenParent || [];
  flattenParentKey = flattenParentKey || "flattenParent";
  const result = [];
  array.forEach(item => {
    const flattenItem = JSON.parse(JSON.stringify(item));
    // flattenItem[flattenParentKey] = flattenParent;
    result.push(flattenItem);
    childrenKeys.forEach(key => {
      if (item[key] && Array.isArray(item[key])) {
        const children = arrayChildrenFlatten(item[key], {
          childrenKeys,
          // flattenParent: [...flattenParent, item],
          flattenParentKey
        });
        result.push(...children);
      }
    });
  });
  return result;
}

export function arrayLookup(data, key, value, targetKey = "") {
  var targetValue = "";
  for (var i = 0; i < data.length; i++) {
    if (data[i][key] == value) {
      if (targetKey == "") {
        targetValue = data[i];
      } else {
        targetValue = data[i][targetKey];
      }
      break;
    }
  }
  return targetValue;
}

/**
 * Generate TXT and download
 * @param string download file name name
 * @param string file content
 */
export function createTxt(filename, text) {
  var element = document.createElement("a");
  element.setAttribute(
    "href",
    "data:text/plain;charset=utf-8," + encodeURIComponent(text)
  );
  element.setAttribute("download", filename);

  element.style.display = "none";
  document.body.appendChild(element);

  element.click();

  document.body.removeChild(element);
}

/**
 * Construct tree structure data
 * @param {*} data data source
 * @param {*} id id field default 'id'
 * @param {*} parentId parent node field default 'parentId'
 * @param {*} children child node field default 'children'
 * @param {*} rootId root Id default 0
 */
export function handleTree(data, id, parentId, children, rootId) {
  id = id || "id";
  parentId = parentId || "parentId";
  children = children || "children";
  rootId =
    rootId ||
    Math.min.apply(
      Math,
      data.map(item => {
        return item[parentId];
      })
    ) ||
    0;
  //Deep clone of source data
  const cloneData = JSON.parse(JSON.stringify(data));
  //loop through all items
  const treeData = cloneData.filter(father => {
    let branchArr = cloneData.filter(child => {
      //returns an array of children of each item
      return father[id] === child[parentId];
    });
    branchArr.length > 0 ? (father.children = branchArr) : "";
    //back to first floor
    return father[parentId] === rootId;
  });
  return treeData != "" ? treeData : data;
}

/**
 * Randomly generate message ID
 */
export function generateUUID() {
  var d = new Date().getTime();
  if (window.performance && typeof window.performance.now === "function") {
    d += performance.now(); //use high-precision timer if available
  }
  var uuid = "xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx".replace(/[xy]/g, function(
    c
  ) {
    var r = (d + Math.random() * 16) % 16 | 0;
    d = Math.floor(d / 16);
    return (c == "x" ? r : (r & 0x3) | 0x8).toString(16);
  });
  return uuid;
}