// Delivery Data Table Merge
export function mergeDeliveryReportTable(data, merge, summaryCol) {
    if (!merge || merge.length === 0) {
        return data;
    }

    // Cyclic data list
    for (let i = 0; i < data.length; i++) {
        let rowData = data[i];

        // Loop through rows that need to be combined
        merge.forEach((mItem) => {
            const mList = {}; // Record data for merged rows
            const record = {
                'os': '',
                'game_name': '',
                'get_ad_delivery_user': ''
            }; // Record the data of the previous line, which is used to judge whether to recalculate and merge with the next line

            // update daily data
            rowData = rowData.map((row, index) => {
                const cellVal = row[mItem]; // The data of the fields that need to be merged in this line

                if (mList[mItem] && mList[mItem][cellVal]) {
                    // Subsequent rows are not merged when the system is not equal
                    if (record.os != row['os']) {
                        mList['os'] = null;
                        mList['game_name'] = null;
                        mList['get_ad_delivery_user'] = null;
                        // mList['dis_num_daily'] = null;
                        // mList['dis_num'] = null;
                    }
                    // The system is the same but the game name is different, subsequent lines are not merged
                    if (record.game_name != row['game_name']) {
                        mList['get_ad_delivery_user'] = null;
                        // mList['dis_num_daily'] = null;
                        // mList['dis_num'] = null;
                    }
                    // if (record.get_ad_delivery_user != row['get_ad_delivery_user']) {
                    //     mList['dis_num_daily'] = null;
                    //     mList['dis_num'] = null;
                    // }
                }

                // update record
                record.os = row['os'];
                record.game_name = row['game_name'];
                record.get_ad_delivery_user = row['get_ad_delivery_user']

                if (mList[mItem] && mList[mItem][cellVal]) { // Records with this field already exist, add one to the merged row
                    rowData[index - mList[mItem][cellVal]][mItem + '-span'].rowspan++;
                    mList[mItem][cellVal]++;
                    row[mItem + '-span'] = {
                        rowspan: 0,
                        colspan: 0
                    };
                } else { // This field has not been recorded yet, initialize the merged row data
                    mList[mItem] = {};
                    mList[mItem][cellVal] = 1;
                    row[mItem + '-span'] = {
                        rowspan: 1,
                        colspan: 1
                    };
                }

                return row; // return the row data
            })
        })

        // Set summary row merged cell data
        for (let j in rowData) {
            let row = rowData[j];
            if (row.summaryFlag) {
                row['game_name-span'] = {
                    rowspan: 1,
                    colspan: summaryCol
                }
                row['get_ad_delivery_user-span'] = {
                    rowspan: 0,
                    colspan: 0
                }
                row['channel-span'] = {
                    rowspan: 0,
                    colspan: 0
                }
            }
        }
    }
    
    return data;
}
