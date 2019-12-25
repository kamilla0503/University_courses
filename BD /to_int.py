from tkinter import *
import pymysql
from tkinter import *
from tkinter.ttk import Combobox
from functools import partial
from tkinter import messagebox
from tkinter import ttk
from tkintertable import *
#from tkintertable.Tables import TableCanvas
#from tkintertable.TableModels import TableModel
from tkintertable import tkintertable

con = pymysql.connect(host='localhost', user='root', passwd="password", db='mydb')
cur = con.cursor()
cur.execute("SET FOREIGN_KEY_CHECKS=0")
window = Tk()
window.title("База данных о мутациях")

#btn = Button(window, text="Не нажимать!")
#btn.grid(column=1, row=0)

def clicked(name_table):
    #lbl.configure(text="Я же просил...")
    #print("what")
    window_show = Tk()
    window_show.title("Просмотр данных")
    window_show.geometry('500x350')
    sq_z = "SELECT * FROM " + str(name_table.get()) + ";"
    print(sq_z)
    cur.execute(sq_z)
    rows = cur.fetchall()


    height = min(15, len(rows) )
    width = len(rows[0])

    cur.execute("DESCRIBE " + str(name_table.get()) + ";")
    ds = cur.fetchall()
    #print(ds)
    for j in range(width):
        b = Label(window_show, text=ds[j][0])
        b.grid(row= 0, column=j)
    for i in range(1, height+1):  # Rows
        for j in range(width):  # Columns
            b = Label(window_show, text=rows[-i][j])
            b.grid(row=i+1, column=j)



    window_show.mainloop()


def output(name_table, all_vars, ds, window_show):

    all_s_to_sql = " insert into " + str(name_table.get()) + "("
    for j in range(len(ds)):

        all_s_to_sql = all_s_to_sql + ds[j][0] + ","

    all_s_to_sql = all_s_to_sql[:-1]
    all_s_to_sql =all_s_to_sql +") values ("
    for j in range(len(ds)):
        print(str(all_vars[j].get()))

        if("float" in ds[j][1].lower() or "int" in ds[j][1] ):
            all_s_to_sql = all_s_to_sql + str(all_vars[j].get())+","
        else:
            all_s_to_sql = all_s_to_sql +'"'+ str(all_vars[j].get()) + '",'
    all_s_to_sql = all_s_to_sql[:-1]
    all_s_to_sql = all_s_to_sql + ");"

    print(all_s_to_sql)
    cur.execute(all_s_to_sql)
    #btn = Button(window_show, text="Ввести", command=partial(output, [all_vars, ds]))
    #btn.grid(column=2, row=3)

    con.commit()

    window_show.destroy()

def clicked1(name_table):
    #lbl.configure(text="Я же просил...")
    #print("what")
    window_show = Tk()
    window_show.title("Добавление данных")
    window_show.geometry('500x350')


    cur.execute("DESCRIBE " + str(name_table.get()) + ";")
    ds = cur.fetchall()
    print(ds)
    all_vars = []
    for j in range(len(ds)):
        all_vars.append(StringVar(window_show))
        b = Label(window_show, text=ds[j][0])
        b.grid(row= 0, column=j)
        txt = Entry(window_show, width=10, textvariable =all_vars[j])
        txt.grid(column=j, row=1)


    #print(all_s_to_sql)
    btn = Button(window_show, text="Ввести", command=partial(output, name_table, all_vars, ds, window_show))
    btn.grid(column=2, row=3)


    window_show.mainloop()

#def OptionCallBack(*args):
    #print variable.get()
window.geometry('500x350')

variable = StringVar(window)
#variable = str()
variable.set("Select From List")
#variable.trace('w', OptionCallBack)

combo = Combobox(window, textvariable=variable)
all_v =  ("Sample", "Variant", "Population", "Phenotype", "Phenotype_Variant", "Population_variant", "Sample_variant")
combo['values'] =all_v
#combo.current(1)  # установите вариант по умолчанию
combo.grid(column=0, row=0)
name_table = combo.current()
print(name_table)
btn = Button(window, text="Посмотреть", command=partial(clicked, variable))
btn.grid(column=1, row=0)

btn = Button(window, text="Добавить", command=partial(clicked1, variable))
btn.grid(column=2, row=0)

def del_sample(id_sampe):
    id_samp = str(id_sampe.get())

    sql_str = "CALL delete_sample(" + id_samp + ");"
    cur.execute(sql_str)
    con.commit()
    messagebox.showinfo('Пациент', 'Удален')

variable1 = StringVar(window)

b = Label(window, text="id_sample")
b.grid(row=4, column=0)
txt = Entry(window, width=10, textvariable=variable1)
txt.grid(column=0, row=5)

btn = Button(window, text="Удалить пациента", command=partial(del_sample, variable1))
btn.grid(column=1, row=5)

window.mainloop()