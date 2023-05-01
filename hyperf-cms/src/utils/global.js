/**
 * General js method encapsulation processing
 * global utility function
 * Copyright (c) 2021
 */

const baseURL = process.env.VUE_APP_BASE_API;

// 日期格式化
export function parseTime(time, pattern) {
  if (arguments.length === 0 || !time) {
    return null;
  }
  const format = pattern || "{y}-{m}-{d} {h}:{i}:{s}";
  let date;
  if (typeof time === "object") {
    date = time;
  } else {
    if (typeof time === "string" && /^[0-9]+$/.test(time)) {
      time = parseInt(time);
    } else if (typeof time === "string") {
      time = time.replace(new RegExp(/-/gm), "/");
    }
    if (typeof time === "number" && time.toString().length === 10) {
      time = time * 1000;
    }
    date = new Date(time);
  }
  const formatObj = {
    y: date.getFullYear(),
    m: date.getMonth() + 1,
    d: date.getDate(),
    h: date.getHours(),
    i: date.getMinutes(),
    s: date.getSeconds(),
    a: date.getDay()
  };
  const time_str = format.replace(/{(y|m|d|h|i|s|a)+}/g, (result, key) => {
    let value = formatObj[key];
    // Note: getDay() returns 0 on Sunday
    if (key === "a") {
      return ["Day", "one", "two", "three", "four", "five", "six"][value];
    }
    if (result.length > 0 && value < 10) {
      value = "0" + value;
    }
    return value || 0;
  });
  return time_str;
}

// form reset
export function resetForm(refName) {
  if (this.$refs[refName]) {
    this.$refs[refName].resetFields();
  }
}

// add date range
export function addDateRange(params, dateRange, propName) {
  var search = params;
  search.params = {};
  if (null != dateRange && "" != dateRange) {
    if (typeof propName === "undefined") {
      search.params["beginTime"] = dateRange[0];
      search.params["endTime"] = dateRange[1];
    } else {
      search.params["begin" + propName] = dateRange[0];
      search.params["end" + propName] = dateRange[1];
    }
  }
  return search;
}

// echo data dictionary
export function selectDictLabel(datas, value) {
  var actions = [];
  Object.keys(datas).some(key => {
    if (datas[key].dict_value == "" + value) {
      actions.push(datas[key].dict_label);
      return true;
    }
  });
  return actions.join("");
}

// echo data dictionary (array of strings)
export function selectDictLabels(datas, value, separator) {
  var actions = [];
  var currentSeparator = undefined === separator ? "," : separator;
  var temp = value.split(currentSeparator);
  Object.keys(value.split(currentSeparator)).some(val => {
    Object.keys(datas).some(key => {
      if (datas[key].dict_value == "" + temp[val]) {
        actions.push(datas[key].dict_label + currentSeparator);
      }
    });
  });
  return actions.join("").substring(0, actions.join("").length - 1);
}

// General Download Method
export function download(fileName) {
  window.location.href =
    baseURL +
    "/common/download?fileName=" +
    encodeURI(fileName) +
    "&delete=" +
    true;
}

// String formatting (%s)
export function sprintf(str) {
  var args = arguments,
    flag = true,
    i = 1;
  str = str.replace(/%s/g, function() {
    var arg = args[i++];
    if (typeof arg === "undefined") {
      flag = false;
      return "";
    }
    return arg;
  });
  return flag ? str : "";
}

// Convert string, undefined, null, etc. into ""
export function praseStrEmpty(str) {
  if (!str || str == "undefined" || str == "null") {
    return "";
  }
  return str;
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
 * Solve the 403 image cache problem
 * @param {*}_url
 */
export function getImages(_url) {
  if (_url != undefined) {
    let _u = _url.substring(7);
    return "https://images.weserv.nl/?url=" + _u;
  }
}
