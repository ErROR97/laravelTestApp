
import xlrd
from collections import OrderedDict
import simplejson as json

# Open the workbook and select the first worksheet


wb = xlrd.open_workbook("/Users/error/Desktop/bank.xlsx")
sh = wb.sheet_by_index(1)
# List to hold dictionaries
employees = []
# Iterate through each row in worksheet and fetch values into dict
for rownum in range(1, sh.nrows):
    employe = OrderedDict()
    row_values = sh.row_values(rownum)
    employees['personnel_id'] = row_values[0]
    employees['national_id'] = row_values[1]
    employees['name'] = row_values[2]
    employees['last_name'] = row_values[3]
    employees['unit'] = row_values[4]
    employees['company'] = row_values[5]
    employees['job'] = row_values[6]
    employees['military'] = row_values[7]
    employees['father_name'] = row_values[8]
    employees['date_of_birth'] = row_values[9]
    employees['place_of_issue'] = row_values[10]
    employees['weight'] = row_values[11]
    employees['height'] = row_values[12]
    employees['blood_type'] = row_values[13]
    employees['bank_account_number'] = row_values[14]
    employees['home_address'] = row_values[15]
    employees['access_type'] = row_values[16]
    employees.append(employe)

# Serialize the list of dicts to JSON
j = json.dumps(employees)
# Write to file
with open('dataEmployees.json', 'w') as f:
    f.write(j)

print(j)
