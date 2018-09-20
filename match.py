import pandas as pd


def load_raw_dataset(filename='dataset.csv'):
    return pd.read_csv(filename)


raw_dataset = load_raw_dataset()
raw_dataset.head()
