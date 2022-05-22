
import xlrd
from collections import OrderedDict
import simplejson as json

# Open the workbook and select the first worksheet


wb = xlrd.open_workbook("/Users/error/Desktop/bank.xlsx")
sh = wb.sheet_by_index(0)
# List to hold dictionaries
soldiers = []
# Iterate through each row in worksheet and fetch values into dict
for rownum in range(1, sh.nrows):
    soldier = OrderedDict()
    row_values = sh.row_values(rownum)
    soldier['personnel_id'] = row_values[0]
    soldier['national_id'] = row_values[1]
    soldier['name'] = row_values[2]
    soldier['last_name'] = row_values[3]
    soldier['unit'] = row_values[4]
    soldier['company'] = row_values[5]
    soldier['job'] = row_values[6]
    soldier['military'] = row_values[7]
    soldier['father_name'] = row_values[8]
    soldier['date_of_birth'] = row_values[9]
    soldier['place_of_issue'] = row_values[10]
    soldier['weight'] = row_values[11]
    soldier['height'] = row_values[12]
    soldier['blood_type'] = row_values[13]
    soldier['bank_account_number'] = row_values[14]
    soldier['home_address'] = row_values[15]
    soldiers.append(soldier)

# Serialize the list of dicts to JSON
j = json.dumps(soldiers)
# Write to file
with open('data.json', 'w') as f:
    f.write(j)

print(j)
